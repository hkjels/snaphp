.PHONY: docs install uninstall

# Determine environment

FLAG:=
ifneq (,$(findstring dev,$(shell echo $$ENV)))
	FLAG:=--dev
endif

# Create documentation

docs:
	bin/doxphp2docco src/Bold/*.php

# Install dependencies using composer
# @see http://getcomposer.org/

install:
	php bin/composer.phar install $(FLAG)

uninstall:
	rm -Rf vendor composer.lock
	ls bin | grep -v composer.phar | xargs rm -f

