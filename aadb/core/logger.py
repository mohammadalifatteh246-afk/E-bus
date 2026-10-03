import logging
import structlog
from core.config import config

def setup_logging() -> None:
    """
    Configures structured JSON logging for the entire platform.
    This ensures all findings, tool executions, and sandbox results
    are easily parsed by the evaluation harness and metrics systems.
    """
    log_level = getattr(logging, config.LOG_LEVEL.upper(), logging.INFO)

    structlog.configure(
        processors=[
            structlog.stdlib.add_log_level,
            structlog.stdlib.add_logger_name,
            structlog.processors.TimeStamper(fmt="iso"),
            structlog.processors.StackInfoRenderer(),
            structlog.processors.format_exc_info,
            structlog.processors.JSONRenderer() if config.ENV == "production" else structlog.dev.ConsoleRenderer(),
        ],
        context_class=dict,
        logger_factory=structlog.stdlib.LoggerFactory(),
        wrapper_class=structlog.stdlib.BoundLogger,
        cache_logger_on_first_use=True,
    )

    logging.basicConfig(
        format="%(message)s",
        stream=sys.stdout,
        level=log_level,
    )

logger = structlog.get_logger()
