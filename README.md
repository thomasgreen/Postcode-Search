# Postcode Search

## Install 

This application uses laravel and sail. To install, run the following commands after cloning the repo.

```bash
cd postcode-search
./vendor/bin/sail up
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

The app should be accessible on at `localhost`.

## Import Postcodes

To import the postcodes, run 

```bash
./vendor/bin/sail artisan app:import-postcodes
```

## Functionality

This application uses the Haversine formula to calculate the distance between the user postcode and stores.

### Future Work

If i had more time on this application, I would add more refinement options to the search endpoints (filter by store type, search by name etc).

I would add full user related endpoints to create tokens, revoke tokens etc.

I would add some kind of dashboard to visualise stores in the system on a map.

## API Endpoints

Note: I have added a artisan command `app:issue-token {id} `which can be used to generate a token for the authentication header in postman,

A postman collection is in the base of this repo to save you setting up the endpoints manually.

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

### GET /stores/can-deliver/{postcode}

Fetch stores that can deliver to a given postcode based on the store's `max_delivery_distance`.

#### Parameters

- `postcode` (string, required): The postcode to search near.
- `distance` (integer, optional): The radius (in km) to search within. If not specified, defaults to the store's `max_delivery_distance`.

#### Response

- `200 OK`: On success, returns a paginated list of stores that can deliver to the given postcode.
- `404 Not Found`: If the postcode is not found.

#### Example Response

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 3,
            "name": "test",
            "latitude": "54.5456820",
            "longitude": "-1.2137320",
            "is_open": 1,
            "store_type": "shop",
            "max_delivery_distance": 10,
            "created_at": "2024-06-26T18:27:39.000000Z",
            "updated_at": "2024-06-26T18:27:39.000000Z",
            "distance": 0
        }
    ],
    "first_page_url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://0.0.0.0/api/stores/near/ts4%203ts",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

#### Note

- The `distance` field in the response indicates the distance from the store to the provided postcode in kilometers.
- Ensure that the `Authorization` header is set with a valid Bearer token when making the request.

### Authorization

This endpoint is protected by the `auth:sanctum` middleware. Ensure you include a valid authentication token in the request headers.

### GET /stores/near/{postcode}

Fetch stores near a given postcode within a specified distance.

#### Parameters

- `postcode` (string, required): The postcode to search near.
- `distance` (integer, optional): The radius (in km) to search within. Default is 10 km.

#### Response

- `200 OK`: On success, returns a paginated list of stores near the given postcode.
- `404 Not Found`: If the postcode is not found.

#### Example Response

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 3,
            "name": "test",
            "latitude": "54.5456820",
            "longitude": "-1.2137320",
            "is_open": 1,
            "store_type": "shop",
            "max_delivery_distance": 10,
            "created_at": "2024-06-26T18:27:39.000000Z",
            "updated_at": "2024-06-26T18:27:39.000000Z",
            "distance": 0
        }
    ],
    "first_page_url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://0.0.0.0/api/stores/near/ts4%203ts?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://0.0.0.0/api/stores/near/ts4%203ts",
    "per_page": 10,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

#### Note

- The `distance` field in the response indicates the distance from the store to the provided postcode in kilometers.
- Ensure that the `Authorization` header is set with a valid Bearer token when making the request.

### Authorization

This endpoint is protected by the `auth:sanctum` middleware. Ensure you include a valid authentication token in the request headers.
