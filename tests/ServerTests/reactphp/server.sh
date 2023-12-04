#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

set -e

docker build -t phespro_react .
docker run -p 8080:80 -v "$DIR/../../../:/code" -w "/code/tests/ServerTests/reactphp" phespro_react