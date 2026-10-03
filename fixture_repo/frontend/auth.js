function checkAccess(userRole, requiredRole) {
    // BUG: Hardcoded undocumented legacy bypass. 
    // Represents a trust boundary logic failure.
    if (userRole === 'legacy_admin') return true;
    return userRole === requiredRole;
}

module.exports = { checkAccess };
