## HTML2MD 

A [Laravel Zero](https://laravel-zero.com/) CLI tool to extract a column with HTML content from the database to markdown file.


### Setup

Clone the repository:

```shell
    git clone https://github.com/marcim/html2md.git
```

Navigate to root folder and install the PHP dependencies:

```shell
    cd html2md && composer install
```

Create `.env` file copying `.env.example`. Open the new `.env` file and replace dumb values with your's database configuration. See more at [Laravel documentation](https://laravel.com/docs/database#configuration) 

```shell
    cp .env.example .env
    vim .env
```


### Use

#### Basic usage

The most simple usage extracts the specified HTML content field to a markdown file format. Just run the command `php html2md extract [relation] [column]` at project root directory, replacing `relation` to your's database table name, and `column` to the column name with HTML content. See below:

```shell
    php html2md extract posts body
```

The outputed files are saved at `md` folder.

#### YAML Header

Most of the time it is extremely convenient to have a markdown file with a YAML header. If the wished header content comes from the same relation as the html content field, this tool could provide this for you.

To achieve this add `--header=[fields,...]` option in command call:

```shell
    php html2md extract posts body --header=field1,field2
```

Notice the `field1` and `field2` are the wished database columns to fill YAML header. You must separate each column name with comma.


TODO:
- Tests
- Customize output file names
- Allow format dates and timezone
- Get data from a relationship
