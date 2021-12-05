# Job harvest & match 0.1

Test symfony task.

## Installation

1) Clone git-hub repository

```bash
git clone https://github.com/lljjvv/symf_test_task.git
```
2) Install dependencies

```bash
composer install
```
3) Run symfony server
```bash
symfony server:start
```
4) Open project in browser following next link

      [http://127.0.0.1:8000/job](http://127.0.0.1:8000/job)
5) Upload mysql dump (dump is in the project directory)
```bash
mysql -u <user> -p < db_backup.dump
```
[Link for upload guide](https://stackoverflow.com/questions/105776/how-do-i-restore-a-dump-file-from-mysqldump)

## Usage

1) To populate CV-table with fake data run in bash (in project directory)
```bash
php bin/console doctrine:fixtures:load
```
2) To create constant CV follow link
```bash
http://127.0.0.1:8000/cv/create
```
3) To create constant job follow link
```bash
http://127.0.0.1:8000/job/create
```
4) To crawl jobs from site follow link below (This link automatically will save parsed data in DB )
```bash
http://127.0.0.1:8000/job/crawl
```
or you can specify the url
```bash
http://127.0.0.1:8000/job/crawl/{url}
```
5) To access full text search form use the link
[http://127.0.0.1:8000/job](http://localhost:8000/job)
6) To see candidates ordered by SCORE use link
[http://127.0.0.1:8000/job/detail](http://localhost:8000/job/detail)

## Conclusion
Thanks for your time.