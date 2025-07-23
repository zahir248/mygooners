# Request System for Services and Products

## Overview
This system allows users to submit requests for new services and products, which are then reviewed and approved/rejected by administrators.

## Features

### For Users (Sellers)
1. **Service Request Form** - Submit new service requests
2. **Product Request Form** - Submit new product requests
3. **Dashboard Integration** - View pending requests and active items
4. **Status Tracking** - See the status of submitted requests

### For Administrators
1. **Pending Reviews** - Review pending service and product requests
2. **Approve/Reject Actions** - Approve or reject submitted requests
3. **Dashboard Overview** - View pending counts and recent requests
4. **Email Notifications** - Users are notified of approval/rejection

## User Flow

### Submitting a Request
1. User logs in and navigates to their dashboard
2. Clicks "Mohon Tambah Perkhidmatan" or "Mohon Tambah Produk"
3. Fills out the request form with required information
4. Submits the form
5. Request is saved with "pending" status
6. User sees success message and pending count updates

### Admin Review Process
1. Admin logs into admin panel
2. Navigates to pending services/products section
3. Reviews submitted requests
4. Approves or rejects each request
5. User receives notification of decision

## Database Changes
- Services and Products already have "pending" status in their enum
- No additional migrations needed

## Routes Added
- `GET /service-request` - Service request form
- `POST /service-request` - Submit service request
- `GET /product-request` - Product request form  
- `POST /product-request` - Submit product request

## Files Created/Modified

### New Files
- `app/Http/Controllers/Client/RequestController.php`
- `resources/views/client/requests/service-request.blade.php`
- `resources/views/client/requests/product-request.blade.php`

### Modified Files
- `routes/web.php` - Added request routes
- `resources/views/client/dashboard.blade.php` - Updated buttons and added pending section

### Existing Admin Files (Already Functional)
- `app/Http/Controllers/Admin/ServiceController.php` - Has approve/reject methods
- `app/Http/Controllers/Admin/ProductController.php` - Has approve/reject methods
- `resources/views/admin/services/pending.blade.php` - Pending services view
- `resources/views/admin/products/pending.blade.php` - Pending products view
- `resources/views/layouts/admin.blade.php` - Navigation with pending counts
- `app/Http/Controllers/Admin/AdminController.php` - Dashboard with pending stats

## Status Values
- **pending** - Request submitted, awaiting admin review
- **active** - Request approved and published
- **rejected** - Request rejected by admin
- **inactive** - Request deactivated

## Security Features
- Only authenticated users can submit requests
- Only sellers can access request forms
- Admin middleware protects admin routes
- File upload validation for images
- Input validation and sanitization

## Usage Instructions

### For Users
1. Ensure you have seller status (is_seller = true)
2. Navigate to dashboard
3. Click request buttons to access forms
4. Fill required fields and submit
5. Monitor dashboard for status updates

### For Admins
1. Access admin panel
2. Check "Pending Services" or "Pending Products" in navigation
3. Review submitted requests
4. Use approve/reject buttons to process requests
5. Monitor dashboard for pending counts

## Testing
- Test service request submission
- Test product request submission
- Test admin approval/rejection process
- Verify pending counts update correctly
- Check success messages display properly 