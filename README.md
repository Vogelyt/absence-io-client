# Absence.io PHP API-Client
## Installation
Comming soon, package is not published yet...

## Local Development
Start the PHP Docker-Container with:  
```

docker compose build

```

Install the composer dependencys with:
```

docker compose run --rm php composer install

```

Add your HAWK_ID and HAWK_KEY to tests/test.php and run the following command to get all users:
```

docker compose run --rm php php tests/test.php

```
