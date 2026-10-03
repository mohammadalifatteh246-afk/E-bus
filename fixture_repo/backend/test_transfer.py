import unittest
from transfer import process_transfer

class TestTransfer(unittest.TestCase):
    def test_successful_transfer(self):
        # Mechanical success test
        self.assertEqual(process_transfer(100.0, 50.0), 50.0)
        
    def test_insufficient_funds(self):
        # Boundary success test
        with self.assertRaises(ValueError):
            process_transfer(10.0, 50.0)

    def test_regression_negative_transfer(self):
        # Hardening: Ensures INV-02 is never violated again
        with self.assertRaises(ValueError):
            process_transfer(100.0, -500.0)
            
    def test_regression_zero_transfer(self):
        # Boundary case mutant check
        with self.assertRaises(ValueError):
            process_transfer(100.0, 0.0)

if __name__ == '__main__':
    unittest.main()
