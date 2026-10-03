from pydantic_settings import BaseSettings, SettingsConfigDict
from typing import Optional

class AppConfig(BaseSettings):
    """
    Typed configuration for the AADB platform.
    Ensures deterministic configuration via environment variables.
    """
    # Environment
    ENV: str = "development"
    DEBUG: bool = True
    LOG_LEVEL: str = "INFO"
    
    # Database
    DATABASE_URL: str = "postgresql+asyncpg://postgres:postgres@localhost:5432/aadb"
    
    # Redis Queue / State
    REDIS_URL: str = "redis://localhost:6379/0"
    
    # Artifact Storage
    ARTIFACT_STORAGE_PATH: str = "/tmp/aadb/artifacts"
    
    # AI/Model Settings (Provider Agnostic)
    DEFAULT_MODEL: str = "gpt-4o"
    ANTHROPIC_API_KEY: Optional[str] = None
    OPENAI_API_KEY: Optional[str] = None
    GEMINI_API_KEY: Optional[str] = None
    
    # Sandbox constraints
    SANDBOX_TIMEOUT_SECONDS: int = 300
    SANDBOX_MEMORY_LIMIT_MB: int = 1024

    model_config = SettingsConfigDict(env_file=".env", env_file_encoding="utf-8", extra="ignore")

# Global singleton for configuration
config = AppConfig()
