## Running

Prerequisite 
- docker
- [docker-compose](https://docs.docker.com/compose/install/) (not really necessary but I don't like typing long commands)
- [composer](https://getcomposer.org/download/) (not sure if really necessary, but I believe since we use a volume mount we may not pull dependencies in the container? I don't really feel inclined to check the container setup for the xamp container)


Run:

#### Dependencies:
```
cd ./codeigniter
composer install
```

```
docker-compose build
```

```
docker-compose up
```

#### Initialisation /Seeding

To initialise the database with some basic test data, send a get request to `/seed`
or use the command line seeder as [described here](https://codeigniter4.github.io/CodeIgniter4/dbmgmt/seeds.html#command-line-seeding)

The seeder name is `AppSeeder`. So e.g.
```
php spark db:seed AppSeeder
```


## Test Accounts (seeded)

#### Regular user account:
```
Username: bob@test.test
Password: myPassword123
```

#### Store owner 1:
```
Username: store@test.test
Password: anotherPasswrod323
```

#### Store owner 2:
```
Username: gsms@test.test
Password: anotherPasswrod323
```

#### Store owner 3:
```
Username: electronica@test.test
Password: anotherPasswrod323
```

#### Store owner 4:
```
Username: tvs@test.test
Password: anotherPasswrod323
```