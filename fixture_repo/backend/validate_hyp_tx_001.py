import sys
import json
from transfer import process_transfer

def run_validation():
    """
    Validator Sandbox execution script.
    Independently verifies HYP-TX-001 by triggering the exploit.
    """
    initial_balance = 100.0
    malicious_amount = -500.0
    
    try:
        # Execute the suspected bad flow
        new_balance = process_transfer(initial_balance, malicious_amount)
        
        # Check if the invariant INV-02 is violated (balance increased)
        if new_balance > initial_balance:
            result = {
                "status": "VULNERABILITY_CONFIRMED",
                "initial_balance": initial_balance,
                "attempted_transfer": malicious_amount,
                "resulting_balance": new_balance,
                "invariant_violated": "INV-02: Balance strictly decreases"
            }
            print(json.dumps(result, indent=2))
            sys.exit(0)
        else:
            print(json.dumps({"status": "MITIGATED", "details": "Balance did not increase."}))
            sys.exit(0)
            
    except Exception as e:
        # If an error is correctly raised, the bug is mitigated
        print(json.dumps({"status": "MITIGATED_BY_EXCEPTION", "error": str(e)}))
        sys.exit(0)

if __name__ == "__main__":
    run_validation()
