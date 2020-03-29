# Prujezd-vozidel-PK - frontend
Frontend pro zobrazení dat o průjezdu vozidel pro Plzeňský kraj

## Configuration

The configuration files are in the `./config/`.
The files `development.json` and `production.json` overwrite the main configuration file `default.json`.

This is the default configuration:
```json
{
  "API_URL": "http://students.kiv.zcu.cz/~valesz/index.php/api/v1",
  "TOKEN_GENERATOR_PATH": "../backend/lib/generateToken.php"
}
```

## Build

Install dependencies:

```bash
$ npm install
```

And build:

```bash
$ npm run build:production
```
Output directory is `./dist`
