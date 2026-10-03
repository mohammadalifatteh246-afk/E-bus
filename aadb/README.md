# AI-Assisted Advanced Bug Debugging (AADB)

A repository-aware, evidence-driven engineering platform that uses deterministic orchestration and LLM reasoning to discover, reproduce, validate, explain, test, and safely repair complex logic bugs.

## Core Principles

1. **Evidence over confidence:** We never treat an LLM assertion as proof of a bug.
2. **Deterministic orchestration:** The control plane decides what to do; LLMs only reason about semantics.
3. **Reproducibility:** All findings must include a reproducible execution trail.

## Repository Structure

- `/apps`: APIs, web workers, and UI
- `/core`: Domain logic, orchestrator, and security policies
- `/agents`: Specialized agents (Scanner, Validator, Repair)
- `/adapters`: Provider-agnostic adapters for LLMs, tools, static analysis, and runners
- `/sandbox`: Disposable execution environment boundaries
- `/evals`: Internal evaluation harness
- `/tests`: Regression and unit tests for the platform itself

## Local Setup

1. Requires Python 3.12+
2. Install dependencies: `pip install -e .[dev]`
3. Run tests: `pytest`
