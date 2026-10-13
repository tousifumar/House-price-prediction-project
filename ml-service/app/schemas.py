"""
Pydantic Schemas for FastAPI House Price ML Service
Matches Section 15 & 16 of the specification.
"""
from typing import List, Optional
from pydantic import BaseModel, Field

class PropertyPredictionRequest(BaseModel):
    property_type: str = Field(default="Apartment", description="Apartment, Villa, Independent House, Builder Floor, Penthouse, Studio")
    transaction_type: str = Field(default="Sale", description="Sale, Rent")
    city: str = Field(default="Bangalore", description="City name")
    locality: str = Field(default="Whitefield", description="Locality within city")
    area: float = Field(..., gt=0, description="Built-up Area in sq.ft")
    carpet_area: Optional[float] = Field(default=None, description="Carpet Area in sq.ft")
    bedrooms: int = Field(default=3, ge=1, le=10, description="Number of bedrooms")
    bathrooms: int = Field(default=2, ge=1, le=10, description="Number of bathrooms")
    balconies: Optional[int] = Field(default=1, ge=0, le=6, description="Number of balconies")
    floor: Optional[int] = Field(default=1, ge=0, le=100, description="Unit floor number")
    total_floors: Optional[int] = Field(default=1, ge=1, le=100, description="Total building floors")
    property_age: Optional[int] = Field(default=0, ge=0, le=100, description="Property age in years")
    furnishing: Optional[str] = Field(default="Semi-Furnished", description="Unfurnished, Semi-Furnished, Fully Furnished")
    parking: Optional[bool] = Field(default=True, description="Dedicated parking available")
    latitude: Optional[float] = None
    longitude: Optional[float] = None
    amenities: Optional[List[str]] = Field(default_factory=list, description="List of society amenities")

class FactorImpact(BaseModel):
    factor_name: str
    factor_type: str  # positive, negative, neutral
    impact_amount: float
    impact_percentage: float
    description: str

class PriceRange(BaseModel):
    min: float
    max: float
    formatted: str

class PredictionResponse(BaseModel):
    success: bool = True
    predicted_price: float
    formatted_price: str
    price_range: PriceRange
    price_per_sqft: float
    confidence_score: float
    currency: str = "INR"
    influencing_factors: List[FactorImpact]
    model_version: str
    algorithm: str
    disclaimer: str

class ModelInfoResponse(BaseModel):
    status: str
    version: str
    algorithm: str
    r2_score: float
    mae: float
    rmse: float
    mape: float
    trained_at: str
    features: List[str]

class HealthResponse(BaseModel):
    status: str
    service: str
    model_loaded: bool
    version: str
