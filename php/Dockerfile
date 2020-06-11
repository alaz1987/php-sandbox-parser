FROM php:5.6

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN ln -s /usr/local/bin/php php

ENTRYPOINT [ "php", "/app/run.php" ]