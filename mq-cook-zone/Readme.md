# MQ Cook Zone

**MQ Cook Zone** is a WordPress plugin designed for user login, registration, and recipe management. It integrates with the **WP Recipe Maker** plugin for advanced recipe features and the **Better Messages** plugin for improved messaging functionality. This plugin allows admins and chefs to manage recipes, track recipe views and likes, create personalized dashboards for chefs, and facilitate better communication between users.

## Features

- **User Login & Registration**: Allows users to register and log in to the website.
- **Custom User Roles**:
  - **Food Lover**: A read-only role for food lovers who wish to explore recipes and interact with posts.
  - **Chef (Active)**: A role for chefs who can create, edit, and publish recipes, as well as upload media.
  - **Chef (Inactive)**: A restricted role for chefs who can only read content but cannot publish or edit posts.
- **Recipe Management**: Admins and chefs (active) can add, edit, and manage recipes.
- **Recipe Views Count**: Track how many times a recipe has been viewed.
- **Recipe Likes Count**: Allow users to like recipes, with a count displayed for each recipe.
- **AJAX Support**: The plugin supports AJAX to handle certain actions like liking recipes, improving user experience without page reloads.
- **WP Recipe Maker Integration**: Seamlessly integrates with **WP Recipe Maker** to provide advanced recipe features, including ingredients, instructions, and rich formatting.
- **Better Messages Integration**: Enhances user communication with features from the **Better Messages** plugin, allowing chefs and food lovers to send private messages directly from the plugin.
- **Custom Shortcodes**: Several shortcodes to manage user interaction, recipe display, and dashboard access.

## Installation

1. **Download the Plugin**:
   - Download the plugin file and unzip it.

2. **Upload the Plugin**:
   - Upload the plugin folder to the `/wp-content/plugins/` directory on your WordPress installation.

3. **Activate the Plugin**:
   - Go to the **Plugins** section in your WordPress admin dashboard, find **MQ Cook Zone**, and click **Activate**.

4. **Install Required Plugins**:
   - For full functionality, install and activate the following plugins:
     - **WP Recipe Maker**: [WP Recipe Maker Plugin](https://wordpress.org/plugins/wp-recipe-maker/)
     - **Better Messages**: [Better Messages Plugin](https://wordpress.org/plugins/better-messages/)

## Shortcodes

Here are the available shortcodes that you can use with **MQ Cook Zone**:

### 1. **`[mqcz_dashboard]`**
   - **Description**: Displays the user dashboard, where chefs can manage their recipes, view stats, and perform other actions like editing or deleting their recipes.
   - **Usage**:
     ```php
     [mqcz_dashboard]
     ```

### 2. **`[mqcz_user_button]`**
   - **Description**: Displays a button that toggles between showing the login or registration form based on the user's login status. This is useful for creating a seamless user experience where users can log in or register with a single click.
   - **Usage**:
     ```php
     [mqcz_user_button]
     ```

### 3. **`[mqcz_register_form]`**
   - **Description**: Outputs the registration form for users to sign up. This form will handle user registration and assign them the default role (Food Lover).
   - **Usage**:
     ```php
     [mqcz_register_form]
     ```

### 4. **`[mqcz_login_form]`**
   - **Description**: Displays the login form for users to log into their accounts. It allows users to access their personalized dashboard or content.
   - **Usage**:
     ```php
     [mqcz_login_form]
     ```

### 5. **`[mqcz_recipe_list]`**
   - **Description**: Displays a list of recipes, including their views and like counts. You can use this shortcode to show a collection of recipes in any post or page.
   - **Usage**:
     ```php
     [mqcz_recipe_list]
     ```

### 6. **`[mqcz_recipe_single id="123"]`**
   - **Description**: Displays a single recipe by its ID. It shows the recipe details, including the recipe title, ingredients, instructions, views, likes, and more. Replace `123` with the ID of the recipe.
   - **Usage**:
     ```php
     [mqcz_recipe_single id="123"]
     ```

### 7. **`[mqcz_likes_count id="123"]`**
   - **Description**: Displays the current like count of a recipe identified by its ID. This shortcode is useful for showing the number of likes directly on the page.
   - **Usage**:
     ```php
     [mqcz_likes_count id="123"]
     ```

### 8. **`[mqcz_views_count id="123"]`**
   - **Description**: Displays the view count of a recipe identified by its ID. This shortcode is useful for displaying how popular a recipe is.
   - **Usage**:
     ```php
     [mqcz_views_count id="123"]
     ```

### 9. **`[mqcz_message_form user_id="123"]`**
   - **Description**: Displays a message form that allows users to send a message to another user, identified by the `user_id`. This can be used to initiate communication between chefs and food lovers.
   - **Usage**:
     ```php
     [mqcz_message_form user_id="123"]
     ```

### 10. **`[mqcz_admin_message]`**
   - **Description**: Displays a message or notification to admins. This shortcode can be used to display important alerts or notifications on the admin dashboard or other admin-controlled pages.
   - **Usage**:
     ```php
     [mqcz_admin_message]
     ```

### Example Usage in Pages or Posts

Here’s how you can combine these shortcodes in your posts and pages to build a complete user experience:

1. **Displaying the Registration Form**:
   - Add the following shortcode to a page to allow users to sign up:
     ```php
     [mqcz_register_form]
     ```

2. **Displaying the Login Form**:
   - Add the following shortcode to a page for the login form:
     ```php
     [mqcz_login_form]
     ```

3. **Displaying the Dashboard for Chefs**:
   - Add the following shortcode to a page where chefs can manage their recipes:
     ```php
     [mqcz_dashboard]
     ```

## Admin Settings

- **Dashboard**: The admin can manage users, assign roles, and moderate content.
- **Role Management**: Customize user roles as needed, including managing active/inactive chefs.
- **WP Recipe Maker Integration**: Allows chefs to manage rich recipes, including ingredients, steps, and nutrition.
- **Better Messages Integration**: Chefs and food lovers can send private messages to one another.

## Customization

You can modify the plugin’s look and feel by editing the `assets/css/style.css` and adding custom styles. The script functionality can be adjusted by editing the `assets/js/script.js`.

## Views and Likes Tracking

The **view count** is tracked every time a user visits a recipe page. This data is stored in the database and can be used to understand which recipes are the most popular.

The **like count** is tracked using AJAX, ensuring that when a user clicks the like button, the count is updated without refreshing the page. The like data is stored in the database for persistent tracking.

