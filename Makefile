.PHONY: docs

# Determine environment

FLAG:=
ifneq (,$(findstring dev,$(shell echo $$ENV)))
	FLAG:=--dev
endif

# Create documentation
# @see https://github.com/avalanche123/doxphp/

docs:
	bin/doxphp2docco src/Snap/*.php

