def process_transfer(sender_balance: float, amount: float) -> float:
    """
    Deducts the transfer amount from the sender's balance.
    """
    if amount <= 0:
        raise ValueError("Transfer amount must be strictly positive")
    if sender_balance >= amount:
        return sender_balance - amount
    raise ValueError("Insufficient funds")
