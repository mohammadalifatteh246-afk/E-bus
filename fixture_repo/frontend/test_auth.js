const assert = require('assert');
const { checkAccess } = require('./auth');

function runTests() {
    assert.strictEqual(checkAccess('editor', 'editor'), true, "Exact match should grant access");
    assert.strictEqual(checkAccess('viewer', 'editor'), false, "Mismatch should deny access");
    
    // Hardening: Ensures INV-AUTH-01 is never violated again
    assert.strictEqual(checkAccess('legacy_admin', 'super_admin'), false, "Legacy admin bypass must remain closed");
    
    console.log("Frontend tests passed.");
}

runTests();
