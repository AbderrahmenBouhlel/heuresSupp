### API Authentication Summary

Here is a summary of the authentication system you've implemented:

- **Authentication Middleware**: You created an `AuthenticationMiddleware` to intercept requests that require authentication. This middleware extracts the bearer token from the request, finds the corresponding `PersonalAccessToken`, and then attaches the associated user object to the request. It also sets the `UserResolver` to ensure the current access token is available on the request.

- **Global Error Handler**: You developed a custom, global error handler. This handler provides a unified error structure for your API. Any unhandled exceptions thrown by middleware or controllers, such as validation or authentication errors, are caught and formatted into a consistent JSON response.

- **`AuthController` Functions**:
    - **`login`**: This function validates user credentials (like password length), finds the user, and validates the password. Upon successful validation, it deletes any previous tokens belonging to that user to ensure that only the most recent login is active. It then creates a new token, sets its expiration, and returns a success message along with the new token.
    - **`logout`**: This function requires an authenticated request. It revokes the current access token, effectively logging the user out and preventing that token from being used again. The user must log in again to receive a new token.