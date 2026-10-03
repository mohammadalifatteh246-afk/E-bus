function checkAccess(userRole, requiredRole) {
    return userRole === requiredRole;
}

module.exports = { checkAccess };
