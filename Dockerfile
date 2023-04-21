# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.1

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS symfony_php

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
		jq \
    make \
    vim \
	;

ARG APCU_VERSION=5.1.19
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		libxslt-dev \
		zlib-dev \
	; \
	apk add --update nodejs npm supervisor; \
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		pcntl \
		intl \
		zip \
		mysqli \
		pdo \
		pdo_mysql \
		xsl \
		bcmath \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		redis \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		redis \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 --start-period=90s CMD ["docker-healthcheck"]


# Caddy installation
# To update Caddy, simply look at the caddy-docker Alpine Dockerfile and replace the relevant bits
# Example for 2.6: https://github.com/caddyserver/caddy-docker/blob/7f509065562f208807c67e0fb8dd9d28788b0d33/2.6/alpine/Dockerfile
RUN apk add --no-cache ca-certificates mailcap

RUN set -eux; \
	mkdir -p \
		/config/caddy \
		/data/caddy \
		/etc/caddy \
		/usr/share/caddy \
	; \
	wget -O /etc/caddy/Caddyfile "https://github.com/caddyserver/dist/raw/8c5fc6fc265c5d8557f17a18b778c398a2c6f27b/config/Caddyfile"; \
	wget -O /usr/share/caddy/index.html "https://github.com/caddyserver/dist/raw/8c5fc6fc265c5d8557f17a18b778c398a2c6f27b/welcome/index.html"

# https://github.com/caddyserver/caddy/releases
ENV CADDY_VERSION v2.6.1

RUN set -eux; \
	apkArch="$(apk --print-arch)"; \
	case "$apkArch" in \
		x86_64)  binArch='amd64'; checksum='fc0c0c115ad0f4e7ca5622dedb95c5c4bc5fc5a44731aa63c6cbc6307d3e6dfe3ae040d6580e2064c5c84bded165c768cac0125fdb9418c923272ccd7fdf19ed' ;; \
		armhf)   binArch='armv6'; checksum='9aa52d748e45069ce15274b09737e5806b5aa355c4e32cdc187d478bdd5c1cf22398e0ac365933f64386d79b11860246b8340a740a25644a8bfa6ed032bc5b2f' ;; \
		armv7)   binArch='armv7'; checksum='d5b026c9f6d4f2aeb9058d6d990d6420439985bd8cbb18f028c6adc7f6a22d3d28a6478958117dbb5051d6537f3b8a9bb61ec8cfa631e33032c7000fa0c44c83' ;; \
		aarch64) binArch='arm64'; checksum='92a2310ba12a790d632a288c285c2aa7be16eb3521212f78644c07d1c65d7f27ec81823a10e7ea4a200a013cb557d335a06c95c94fdf1f2359f47f4974b6e37a' ;; \
		ppc64el|ppc64le) binArch='ppc64le'; checksum='c12b3660a7cf0b359d8153bc6be4b7dedf1168e7984fccbf6cf804abaefcbf5edd10651de6c0f7541e8eaefbcdc25eb00b5c2500b8b445e7f8a9382e0fa3ced1' ;; \
		s390x)   binArch='s390x'; checksum='da1d8d60547de3122603134394b3e2bbe18b7fbecc0bba0d24352c7dd765bec6f2cadc93b00ae69369f14e1800a2318cc94af3ab940710a622bf7be4f713bf0b' ;; \
		*) echo >&2 "error: unsupported architecture ($apkArch)"; exit 1 ;;\
	esac; \
	wget -O /tmp/caddy.tar.gz "https://github.com/caddyserver/caddy/releases/download/v2.6.1/caddy_2.6.1_linux_${binArch}.tar.gz"; \
	echo "$checksum  /tmp/caddy.tar.gz" | sha512sum -c; \
	tar x -z -f /tmp/caddy.tar.gz -C /usr/bin caddy; \
	rm -f /tmp/caddy.tar.gz; \
	chmod +x /usr/bin/caddy; \
	caddy version

# set up nsswitch.conf for Go's "netgo" implementation
# - https://github.com/docker-library/golang/blob/1eb096131592bcbc90aa3b97471811c798a93573/1.14/alpine3.12/Dockerfile#L9
# RUN [ ! -e /etc/nsswitch.conf ] && echo 'hosts: files dns' > /etc/nsswitch.conf

# See https://caddyserver.com/docs/conventions#file-locations for details
ENV XDG_CONFIG_HOME /config
ENV XDG_DATA_HOME /data

EXPOSE 80
EXPOSE 443
EXPOSE 443/udp
EXPOSE 2019

COPY --from=dunglas/mercure:v0.11 /srv/public /srv/mercure-assets/
COPY --from=eckinox/caddy-waf:latest /usr/bin/caddy /usr/bin/caddy
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile

# Add Coraza Web Application Firewall configurations
COPY docker/caddy/coraza.conf /etc/caddy/coraza.conf
COPY docker/caddy/coraza-eckinox-overwrites.conf /etc/caddy/coraza-eckinox-overwrites.conf
COPY docker/caddy/coraza-project-overwrites.conf /etc/caddy/coraza-project-overwrites.conf
RUN wget -O coreruleset.zip https://github.com/coreruleset/coreruleset/archive/refs/tags/v3.3.2.zip; \
	unzip coreruleset.zip; \
	rm coreruleset.zip; \
	mv coreruleset-* /etc/caddy/coreruleset;
# Some CRS rules require a PCRE engine.
# This replaces them by the recommended alternative (or simply disables them if no alternative is available)
# Learn more about this at https://github.com/coreruleset/coreruleset/pull/1868
COPY docker/caddy/coreruleset-overwrites/rules/REQUEST-920-PROTOCOL-ENFORCEMENT.conf /etc/caddy/coreruleset/rules/REQUEST-920-PROTOCOL-ENFORCEMENT.conf
COPY docker/caddy/coreruleset-overwrites/rules/REQUEST-942-APPLICATION-ATTACK-SQLI.conf /etc/caddy/coreruleset/rules/REQUEST-942-APPLICATION-ATTACK-SQLI.conf
COPY docker/caddy/coreruleset-overwrites/rules/RESPONSE-953-DATA-LEAKAGES-PHP.conf /etc/caddy/coreruleset/rules/RESPONSE-953-DATA-LEAKAGES-PHP.conf

COPY docker/php/supervisor/supervisord.conf /etc/supervisord.conf

COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

# Allow to choose skeleton
ARG SKELETON="symfony/website-skeleton"
ENV SKELETON ${SKELETON}

# Allow to use development versions of Symfony
ARG STABILITY="stable"
ENV STABILITY ${STABILITY:-stable}

# Allow to select skeleton version
ARG SYMFONY_VERSION=""
ENV SYMFONY_VERSION ${SYMFONY_VERSION}

# Allow passing the Symfony secrets decryption key via an environment variable
ARG SYMFONY_DECRYPTION_SECRET=""
ENV SYMFONY_DECRYPTION_SECRET=${SYMFONY_DECRYPTION_SECRET}

# Define PHP configuration
ARG APP_ENV="prod"
ENV APP_ENV ${APP_ENV:-prod}
RUN if [ "$APP_ENV" = "prod" ]; then \
			ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini; \
		else \
			ln -s $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini; \
		fi;
COPY docker/php/conf.d/symfony.${APP_ENV}.ini $PHP_INI_DIR/conf.d/symfony.ini

# Provide git with the GitHub access token required to access private repositories
ARG GITHUB_ACCESS_TOKEN=""
ENV GITHUB_ACCESS_TOKEN ${GITHUB_ACCESS_TOKEN}
RUN composer config --global github-oauth.github.com ${GITHUB_ACCESS_TOKEN}

# Download the Symfony skeleton and leverage Docker cache layers
RUN composer create-project "${SKELETON} ${SYMFONY_VERSION}" . --stability=$STABILITY --prefer-dist --no-dev --no-progress --no-interaction; \
	composer clear-cache

###> recipes ###
###< recipes ###

###> eckinox/pdf-bundle ###
# Based on https://github.com/puppeteer/puppeteer/blob/main/docs/troubleshooting.md#running-on-alpine
# > Tell Puppeteer to skip installing Chrome. We'll be using the installed package.
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
# > Only install Puppeteer and Chromium if the project uses the eckinox/pdf-bundle
# We use this format of COPY to prevent failure if composer.json doesn't exist yet
COPY .env composer.jso[n] /srv/app/

RUN if [ -f composer.json ] && grep -q "eckinox/pdf-bundle" composer.json; then \
		apk add --no-cache \
      chromium \
      nss \
      freetype \
      harfbuzz \
      ca-certificates \
      ttf-freefont; \
			npm install --global --unsafe-perm puppeteer@13.5.0; \
	fi;
###< eckinox/pdf-bundle ###

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	if [ "$APP_ENV" = "prod" ]; then \
		composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer symfony:dump-env prod; \
		composer run-script --no-dev post-install-cmd; \
	else \
		composer install --prefer-dist --no-progress --no-scripts --no-interaction; \
		composer dump-autoload --classmap-authoritative; \
		composer symfony:dump-env dev; \
		composer run-script --dev post-install-cmd; \
	fi; \
	if [ -f package-lock.json ]; then \
		npm ci --production; \
	# npm run encore production; \ # @TODO: Determine if we want to use Encore or not.
	fi; \
	chmod +x bin/console; sync
VOLUME /srv/app/var

# Ensure file permissions are adequate
RUN chown -R www-data:www-data .

ENTRYPOINT ["docker-entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
