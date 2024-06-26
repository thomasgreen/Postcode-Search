# Postcode Search

## Install 

This application uses laravel and sail. To install, run the following commands after cloning the repo.

```bash
cd postcode-search
./vendor/bin/sail up
./vendor/bin/sail artisan migrate
```

The app should be accessible on at `localhost`.

## Import Postcodes

## API Endpoints

### POST /stores

Create a new store.

#### Parameters

- `name` (string, required): The name of the store.
- `latitude` (decimal, required): The latitude of the store.
- `longitude` (decimal, required): The longitude of the store.
- `is_open` (boolean, required): The open/closed status of the store.
- `store_type` (string, required): The type of the store. One of: takeaway, shop, restaurant.
- `max_delivery_distance` (integer, required): The maximum delivery distance for the store.

#### Response

- `201 Created`: On success, returns the created store object.
- `422 Unprocessable Entity`: On validation failure, returns validation errors.
