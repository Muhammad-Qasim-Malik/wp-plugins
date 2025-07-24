# MQ Members

**MQ Members** is a WordPress plugin designed for user signups, logins, and membership management. It integrates with Stripe to manage **Paid Member** subscriptions and provides a user dashboard where members can create and manage posts. The plugin displays fields according to the user’s role (Free or Paid), allowing for personalized content and management.

## Features

- **User Signup & Login**: Includes shortcodes for user signup and login forms.
- **Stripe Integration**: Integrated with Stripe for handling payments for the **Paid Member** plan.
- **Membership Plans**: Two membership plans available:
  - **Free Member**: Allows basic access to the site.
  - **Paid Member**: Provides access to premium content and additional features.
- **User Dashboard**: Paid members can manage their posts from the dashboard.
- **Role-Based Content Display**: Displays different fields and options depending on the user’s role (Free Member vs. Paid Member).
- **Post Management**: Members can add, edit, and manage their posts directly from their dashboard.

## Installation

1. **Download the Plugin**:
   - Download the plugin ZIP file and unzip it.

2. **Upload the Plugin**:
   - Upload the plugin folder to the `/wp-content/plugins/` directory on your WordPress installation.

3. **Activate the Plugin**:
   - Go to the **Plugins** section in your WordPress admin dashboard, find **MQ Members**, and click **Activate**.

4. **Install Stripe Library**:
   - The plugin uses the Stripe PHP library for payment processing. It is automatically included if you use Composer to install the plugin dependencies, or you can manually add it by running:
     ```bash
     composer install
     ```

5. **Set Up Stripe Keys**:
   - Replace `'Your Stripe Secret Key'` and `'Your Stripe Publishable Key'` in the plugin code with your actual [Stripe API keys](https://dashboard.stripe.com/test/apikeys).

6. **Add Roles**:
   - The plugin automatically adds two custom user roles upon activation: **Paid Member** and **Free Member**. These roles control access to different content.

## Shortcodes

### 1. **`[mq_user_button]`**
   - **Description**: Displays a button that toggles between the login and signup forms depending on the user’s login status. If the user is not logged in, it will show the login/signup options. If they are logged in, it will display the user’s role and dashboard link.
   - **Usage**:
     ```php
     [mq_user_button]
     ```

### 2. **`[mq_dashboard]`**
   - **Description**: Displays the user dashboard, where members can manage their posts and content. The dashboard will show different options based on the user’s role (e.g., Paid Members will see post management options).
   - **Usage**:
     ```php
     [mq_dashboard]
     ```

### 3. **`[mq_login_form]`**
   - **Description**: Displays the login form for users who have already registered. Allows them to log in and access their dashboard.
   - **Usage**:
     ```php
     [mq_login_form]
     ```

### 4. **`[mq_signup_form]`**
   - **Description**: Outputs the signup form for users to sign up. This form will allow users to choose between becoming a **Free Member** or **Paid Member** (depending on the setup).
   - **Usage**:
     ```php
     [mq_signup_form]
     ```

### Example Usage in Pages or Posts

1. **Displaying the Signup Form**:
   - Add the following shortcode to a page where users can register:
     ```php
     [mq_signup_form]
     ```

2. **Displaying the Login Form**:
   - Add the following shortcode to a page for users to log in:
     ```php
     [mq_login_form]
     ```

3. **Displaying the Member Dashboard**:
   - Add the following shortcode to a page where users can manage their posts:
     ```php
     [mq_dashboard]
     ```

4. **Displaying the User Action Button**:
   - Add the following shortcode to allow users to sign up or log in, based on their status:
     ```php
     [mq_user_button]
     ```

5. **Displaying a Single Post**:
   - Add the following shortcode to display a specific post by its ID:
     ```php
     [mq_single_post id="123"]
     ```

## Admin Settings

- **Manage Membership Roles**: Admins can manage the **Paid Member** and **Free Member** roles directly from the **Users** section in the WordPress admin.
- **Stripe Integration**: Admins must input their Stripe API keys in the plugin configuration file (`STRIPE_SECRET_KEY` and `STRIPE_PUBLISHABLE_KEY`) to enable payment processing.
- **Post Management**: Paid members can add, edit, and manage their posts directly from the member dashboard. Free members can also add posts, but with limited functionality.

## Role-Based Content Display

The plugin allows for dynamic content display based on the user’s role. For example:

- **Free Member**: Only has access to limited content and features.
- **Paid Member**: Gains access to premium content and additional features in the dashboard, such as creating and managing premium posts.
  
### Example of Role-Based Field Display:

- In the **Dashboard**, Paid Members will have access to the full post management system, while Free Members will only see basic options.
- When displaying content (e.g., a post), the fields shown can be customized depending on the user role. For instance, a Paid Member might see additional premium fields or options in the post editor.

## Customization

You can modify the plugin’s appearance by editing the `assets/css/style.css` file. Custom styles can be added to the signup forms, login forms, and member dashboard.

The plugin includes `assets/js/script.js` for frontend interactivity. You can modify this script to customize frontend behavior, such as form validations or AJAX handling.

## Payment Processing

The **MQ Members** plugin integrates Stripe to manage payments for the **Paid Member** plan. Here's how it works:

1. **Stripe Setup**: After setting your **Stripe Secret Key** and **Publishable Key**, users will be able to pay using their credit card information.

3. **Post Payment**: Upon successful payment, the user will be assigned the **Paid Member** role and will gain access to premium content and the ability to manage posts through their dashboard.

## Support

For any issues or inquiries, please open an issue on the [GitHub repository](https://github.com/Muhammad-Qasim-Malik/wp-plugins).

## Changelog

### 1.0
- Initial release of the plugin with user registration, login, and payment processing via Stripe.
- Added custom roles for **Free Member** and **Paid Member**.
- Member dashboard functionality for managing posts.
- Display of fields based on user roles.

---

**Author**: Muhammad Qasim  
**Text Domain**: `mq-members`
