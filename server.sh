#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker build -t phespro .
docker run -p 8080:8080 -v "$DIR:/code" -w "/code" phespro