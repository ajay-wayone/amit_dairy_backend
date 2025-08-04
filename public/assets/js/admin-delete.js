// Admin Delete Functionality
$(document).ready(function() {
    // Generic delete function for all admin pages
    $(document).on('click', '.delete-item', function(e) {
        e.preventDefault();
        
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        const itemType = $(this).data('type') || 'item';
        const deleteUrl = $(this).data('url') || window.location.pathname + '/' + itemId;
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete "${itemName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = $('<form>', {
                    'method': 'POST',
                    'action': deleteUrl
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Category specific delete
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Category?',
            text: `Are you sure you want to delete "${categoryName}"? This will also delete all associated subcategories and products.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/categories/${categoryId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Product specific delete
    $(document).on('click', '.delete-product', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Product?',
            text: `Are you sure you want to delete "${productName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/products/${productId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Order specific delete
    $(document).on('click', '.delete-order', function(e) {
        e.preventDefault();
        
        const orderId = $(this).data('id');
        const orderNumber = $(this).data('number');
        
        Swal.fire({
            title: 'Delete Order?',
            text: `Are you sure you want to delete order "${orderNumber}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/orders/${orderId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Customer specific delete
    $(document).on('click', '.delete-customer', function(e) {
        e.preventDefault();
        
        const customerId = $(this).data('id');
        const customerName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Customer?',
            text: `Are you sure you want to delete customer "${customerName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/customers/${customerId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Banner specific delete
    $(document).on('click', '.delete-banner', function(e) {
        e.preventDefault();
        
        const bannerId = $(this).data('id');
        const bannerName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Banner?',
            text: `Are you sure you want to delete banner "${bannerName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/banners/${bannerId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Box specific delete
    $(document).on('click', '.delete-box', function(e) {
        e.preventDefault();
        
        const boxId = $(this).data('id');
        const boxName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Box?',
            text: `Are you sure you want to delete box "${boxName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/boxes/${boxId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Testimonial specific delete
    $(document).on('click', '.delete-testimonial', function(e) {
        e.preventDefault();
        
        const testimonialId = $(this).data('id');
        const testimonialName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Testimonial?',
            text: `Are you sure you want to delete testimonial from "${testimonialName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/testimonials/${testimonialId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // FAQ specific delete
    $(document).on('click', '.delete-faq', function(e) {
        e.preventDefault();
        
        const faqId = $(this).data('id');
        const faqQuestion = $(this).data('question');
        
        Swal.fire({
            title: 'Delete FAQ?',
            text: `Are you sure you want to delete this FAQ? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/faqs/${faqId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Newsletter specific delete
    $(document).on('click', '.delete-newsletter', function(e) {
        e.preventDefault();
        
        const newsletterId = $(this).data('id');
        const newsletterEmail = $(this).data('email');
        
        Swal.fire({
            title: 'Delete Newsletter Subscription?',
            text: `Are you sure you want to delete subscription for "${newsletterEmail}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/newsletters/${newsletterId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Contact specific delete
    $(document).on('click', '.delete-contact', function(e) {
        e.preventDefault();
        
        const contactId = $(this).data('id');
        const contactName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Contact Enquiry?',
            text: `Are you sure you want to delete enquiry from "${contactName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/contacts/${contactId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });

    // Delivery Location specific delete
    $(document).on('click', '.delete-delivery-location', function(e) {
        e.preventDefault();
        
        const locationId = $(this).data('id');
        const locationName = $(this).data('name');
        
        Swal.fire({
            title: 'Delete Delivery Location?',
            text: `Are you sure you want to delete delivery location "${locationName}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('<form>', {
                    'method': 'POST',
                    'action': `/admin/delivery-locations/${locationId}`
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': $('meta[name="csrf-token"]').attr('content')
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                $('body').append(form);
                form.submit();
            }
        });
    });
}); 