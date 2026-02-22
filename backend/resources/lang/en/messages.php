<?php

return [
    'profile' => [
        'profile_updated' => 'Profile updated successfully.',
        'password_changed' => 'Password changed successfully.',
        'incorrect_password' => 'Incorrect password.',
        'account_deleted' => 'Your account has been successfully deleted.',
        'account_delete_error' => 'Error deleting account: :error',
        'avatar_updated' => 'Avatar updated successfully.',
        'validation_error' => 'Validation error',
        'avatar_upload_error' => 'Error uploading avatar: :error',
    ],
    'order' => [
        'created_successfully' => 'Order created successfully.',
        'create_failed' => 'Failed to create order: :error',
        'unauthorized' => 'Unauthorized',
        'updated_successfully' => 'Order status updated.',
        'no_email' => 'No email address found in order',
        'confirmation_email_sent' => 'Confirmation email sent successfully.',
        'email_send_failed' => 'Failed to send email: :error',
    ],
    'product' => [
        'not_found' => 'Product not found',
        'unauthorized' => 'Unauthorized',
        'review_not_found' => 'Review not found or unauthorized',
        'review_not_found_delete' => 'Review not found',
        'review_unauthorized_delete' => 'Unauthorized to delete this review',
        'review_deleted' => 'Review deleted successfully.',
    ],
    'cart' => [
        'item_added' => 'Item added to cart',
        'item_updated' => 'Cart item updated',
        'item_removed' => 'Item removed from cart',
        'cleared' => 'Cart cleared',
    ],
    'favorite' => [
        'added' => 'Added to favorites',
        'removed' => 'Removed from favorites',
        'synchronized' => 'Favorites synchronized',
        'unauthenticated' => 'Unauthenticated',
    ],
    'contact' => [
        'message_sent' => 'Message sent successfully.',
        'send_failed' => 'Failed to send message.',
    ],
    'auth' => [
        'logged_out' => 'Logged out successfully.',
        'invalid_domain' => 'Email domain does not have MX records or does not exist.',
        'password_reset_sent' => 'Password reset link sent successfully.',
        'password_reset_failed' => 'Failed to send password reset link.',
        'email_verified' => 'Email verified successfully.',
        'email_verification_sent' => 'Verification email sent successfully.',
    ],
];
