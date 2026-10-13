# House Price Predictor — REST API Documentation

## Authentication
API endpoints use Laravel Sanctum Bearer tokens or authenticated web sessions.

### Headers
```
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

---

## Prediction Endpoints

### 1. Predict Property Price
- **Method**: `POST /api/predict`
- **Description**: Evaluates property specifications against the active ML model.
- **Request Body**:
```json
{
  "property_type": "Apartment",
  "transaction_type": "Sale",
  "city": "Bangalore",
  "locality": "Whitefield",
  "area": 1500,
  "carpet_area": 1300,
  "bedrooms": 3,
  "bathrooms": 2,
  "balconies": 2,
  "floor": 8,
  "total_floors": 15,
  "property_age": 5,
  "furnishing": "Semi-Furnished",
  "parking": true,
  "latitude": 12.9698,
  "longitude": 77.7499,
  "amenities": ["Gym", "Lift", "Security", "Swimming Pool"]
}
```
- **Response**: `200 OK`
```json
{
  "success": true,
  "data": {
    "id": 101,
    "predicted_price": 14200000,
    "formatted_price": "₹1.42 Cr",
    "price_range": {
      "min": 13200000,
      "max": 15200000,
      "formatted": "₹1.32 Cr — ₹1.52 Cr"
    },
    "price_per_sqft": 9467,
    "confidence_score": 87,
    "influencing_factors": [
      {"factor": "Location", "impact": 4100000, "type": "positive"},
      {"factor": "Area", "impact": 3200000, "type": "positive"},
      {"factor": "Bedrooms", "impact": 1800000, "type": "positive"},
      {"factor": "Property Age", "impact": -500000, "type": "negative"}
    ]
  }
}
```

---

## Locations & Localities
- `GET /api/locations`: List of active cities.
- `GET /api/localities?city_id={id}`: Localities with benchmark metrics.

## Saved Properties & Alerts
- `GET /api/saved-properties`: Retrieve user saved properties.
- `POST /api/saved-properties`: Save a property.
- `POST /api/price-alerts`: Create a new price alert.
