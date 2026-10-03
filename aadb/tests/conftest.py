import pytest

@pytest.fixture
def mock_repository_path(tmp_path):
    """
    Fixture providing a temporary directory imitating a target repository
    for unit testing the Scanner and Validator agents without side effects.
    """
    repo = tmp_path / "mock_repo"
    repo.mkdir()
    (repo / "main.py").write_text("def hello():\n    return 'world'\n")
    return repo
