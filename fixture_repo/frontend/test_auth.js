const assert = require('assert');
const { checkAccess } = require('./auth');

function runTests() {
    assert.strictEqual(checkAccess('editor', 'editor'), true, "Exact match should grant access");
    assert.strictEqual(checkAccess('viewer', 'editor'), false, "Mismatch should deny access");
    console.log("Frontend tests passed.");
}

runTests();
