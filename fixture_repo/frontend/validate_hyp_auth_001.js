const { checkAccess } = require('./auth');

function runValidation() {
    const requiredRole = 'super_admin';
    const attackerRole = 'legacy_admin';
    
    // Execute the suspected bad flow
    const accessGranted = checkAccess(attackerRole, requiredRole);
    
    // Check if the invariant is violated (access granted without matching role)
    if (accessGranted && attackerRole !== requiredRole) {
        const result = {
            status: "VULNERABILITY_CONFIRMED",
            attacker_role: attackerRole,
            required_role: requiredRole,
            access_granted: accessGranted,
            invariant_violated: "INV-AUTH-01: Only users with the exact required role should gain access."
        };
        console.log(JSON.stringify(result, null, 2));
        process.exit(0);
    } else {
        console.log(JSON.stringify({ status: "MITIGATED", details: "Access correctly denied." }));
        process.exit(0);
    }
}

runValidation();
