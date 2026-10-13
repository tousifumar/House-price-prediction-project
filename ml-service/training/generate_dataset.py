"""
Bangalore Real Estate Dataset Generator
Generates a realistic 3,000-sample housing dataset matching Section 17 of the system specification.
"""
import os
import random
import numpy as np
import pandas as pd

# Micro-market benchmark definitions for Bangalore
LOCALITY_PROFILES = {
    'Whitefield': {'base_rate': 8400, 'lat': 12.9698, 'lng': 77.7499, 'score': 9.1},
    'Electronic City': {'base_rate': 5800, 'lat': 12.8452, 'lng': 77.6602, 'score': 8.6},
    'HSR Layout': {'base_rate': 11200, 'lat': 12.9121, 'lng': 77.6446, 'score': 9.4},
    'Koramangala': {'base_rate': 13500, 'lat': 12.9352, 'lng': 77.6245, 'score': 9.6},
    'Indiranagar': {'base_rate': 15000, 'lat': 12.9784, 'lng': 77.6408, 'score': 9.7},
    'Sarjapur Road': {'base_rate': 7900, 'lat': 12.9237, 'lng': 77.6835, 'score': 9.0},
    'Marathahalli': {'base_rate': 7200, 'lat': 12.9591, 'lng': 77.6974, 'score': 8.7},
    'Hebbal': {'base_rate': 10400, 'lat': 13.0358, 'lng': 77.5970, 'score': 9.2},
    'Yelahanka': {'base_rate': 6900, 'lat': 13.1007, 'lng': 77.5963, 'score': 8.8},
    'Bannerghatta Road': {'base_rate': 7100, 'lat': 12.8984, 'lng': 77.5996, 'score': 8.7},
}

PROPERTY_TYPE_FACTORS = {
    'Apartment': 1.00,
    'Villa': 1.28,
    'Independent House': 1.15,
    'Builder Floor': 0.95,
    'Penthouse': 1.35,
    'Studio': 0.92,
}

FURNISHING_FACTORS = {
    'Unfurnished': 0.96,
    'Semi-Furnished': 1.00,
    'Fully Furnished': 1.07,
}

def generate_dataset(num_samples: int = 3000, seed: int = 42) -> pd.DataFrame:
    random.seed(seed)
    np.random.seed(seed)

    records = []
    localities = list(LOCALITY_PROFILES.keys())

    for i in range(1, num_samples + 1):
        locality = random.choice(localities)
        loc_profile = LOCALITY_PROFILES[locality]

        # Property type distribution (mostly apartments)
        p_type = random.choices(
            list(PROPERTY_TYPE_FACTORS.keys()),
            weights=[0.68, 0.10, 0.08, 0.07, 0.04, 0.03]
        )[0]

        # Bedrooms distribution based on property type
        if p_type == 'Studio':
            bedrooms = 1
            area = int(np.random.normal(550, 60))
        elif p_type == 'Villa':
            bedrooms = random.choice([3, 4, 5])
            area = int(np.random.normal(2800, 450))
        elif p_type == 'Penthouse':
            bedrooms = random.choice([3, 4, 5])
            area = int(np.random.normal(2600, 350))
        else:
            bedrooms = random.choices([1, 2, 3, 4], weights=[0.10, 0.45, 0.38, 0.07])[0]
            base_area_map = {1: 650, 2: 1150, 3: 1600, 4: 2300}
            area = int(np.random.normal(base_area_map[bedrooms], 120))

        area = max(400, area)
        carpet_ratio = random.uniform(0.78, 0.86)
        carpet_area = round(area * carpet_ratio, 1)

        # Bathrooms & balconies
        bathrooms = max(1, min(bedrooms + random.choice([0, 1]), 6))
        balconies = random.choice([1, 2, 3]) if bedrooms >= 2 else random.choice([0, 1])

        # Floors
        if p_type in ['Villa', 'Independent House']:
            total_floors = random.choice([2, 3])
            floor = random.choice([1, 2, total_floors])
        else:
            total_floors = random.choice([8, 12, 14, 18, 24, 30])
            floor = random.randint(1, total_floors)

        property_age = random.choices([0, 1, 2, 3, 5, 8, 12, 18], weights=[0.15, 0.20, 0.18, 0.15, 0.12, 0.10, 0.07, 0.03])[0]
        furnishing = random.choices(list(FURNISHING_FACTORS.keys()), weights=[0.25, 0.55, 0.20])[0]
        parking = 1 if random.random() > 0.12 else 0

        # Small jitter on coordinates
        lat = round(loc_profile['lat'] + random.uniform(-0.015, 0.015), 6)
        lng = round(loc_profile['lng'] + random.uniform(-0.015, 0.015), 6)

        amenities_count = random.randint(4, 14)

        # Economic price calculation with real-estate logic
        base_rate = loc_profile['base_rate']
        type_factor = PROPERTY_TYPE_FACTORS[p_type]
        furn_factor = FURNISHING_FACTORS[furnishing]

        floor_premium = 1.0 + (max(0, floor - 2) * 0.0035)
        age_depreciation = max(0.75, 1.0 - (property_age * 0.012))
        amenity_bonus = 1.0 + ((amenities_count - 5) * 0.008)
        parking_bonus = 1.03 if parking == 1 else 0.98

        noise = np.random.normal(1.0, 0.045)  # 4.5% market variation

        effective_rate = base_rate * type_factor * furn_factor * floor_premium * age_depreciation * amenity_bonus * parking_bonus * noise
        raw_price = area * effective_rate
        price = round(raw_price, -4)  # Round to nearest 10,000

        records.append({
            'id': i,
            'city': 'Bangalore',
            'locality': locality,
            'property_type': p_type,
            'area': float(area),
            'carpet_area': float(carpet_area),
            'bedrooms': int(bedrooms),
            'bathrooms': int(bathrooms),
            'balconies': int(balconies),
            'floor': int(floor),
            'total_floors': int(total_floors),
            'property_age': int(property_age),
            'furnishing': furnishing,
            'parking': int(parking),
            'latitude': lat,
            'longitude': lng,
            'amenities_count': int(amenities_count),
            'price': float(price),
        })

    return pd.DataFrame(records)

if __name__ == '__main__':
    script_dir = os.path.dirname(os.path.abspath(__file__))
    output_dir = os.path.join(script_dir, '..', 'datasets')
    os.makedirs(output_dir, exist_ok=True)
    out_path = os.path.join(output_dir, 'bangalore_house_prices.csv')

    df = generate_dataset(3000)
    df.to_csv(out_path, index=False)
    print(f"Generated {len(df)} records saved to {os.path.abspath(out_path)}")
    print(df[['locality', 'property_type', 'area', 'bedrooms', 'price']].head())
