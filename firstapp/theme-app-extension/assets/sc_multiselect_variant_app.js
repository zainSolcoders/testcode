jQuery(document).ready(function($) {
    const shop = $("#shop").val();
    const productid = $(".smsva_wrapper").attr('data-productid');
    var data = {
        shop: shop,
        productid: productid,
    };

    fetch("/apps/smsva_local/get_settings", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (response) {
            var settings  = response.include_products;

        })
    fetch("/apps/smsva_local/get_variants", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (response) {
        if(response == 'No Record Found'){
            jQuery(".smsva_wrapper").empty();
            jQuery(".smsva_wrapper").hide();
        }
        else{
            jQuery("#variants").append(response);
            jQuery(".smsva_wrapper").show();
        }
    })
    // on Add to Cart
    $(document.body).on("click", ".smsva-add-to-cart-btn", function(e) {
        var items = [{}];
        $('.variant-select.selected').each(function(i, obj) {
            items.push({
                id: $(this).attr("data-id"),
                quantity: 1
            });
        });
        items = items.slice(1, items.length);

        if(items.length == 0){
            Swal.fire({
                title: "Must have to select at least one.",
                icon: "error",
            });
        }
        else{

            let formData = {
                items: items
            };

            fetch(window.Shopify.routes.root + "cart/add.js", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(formData),
            })
            .then((response) => {
                Swal.fire({
                    title: "Product Added to Cart.",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500,
                });
            })
            .catch((error) => {
                Swal.fire({
                    title: "Product does not add to cart.",
                    text: error,
                    icon: "error",
                });
                alert("Error:", error);
            });
        }
    });
    $(document.body).on("click", ".variant-select", function(e) {
        var id = parseInt($(this).attr("data-id"));
        if($('.variant-select[data-id='+id+']').hasClass("selected")){
            $('.variant-select[data-id='+id+']').removeClass("selected");
        }
        else{
            $('.variant-select[data-id='+id+']').addClass("selected");
        }
    });

    fetch("/apps/smsva_local/check_billing", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        if (!result.billing) {
            setTimeout(function() {
                $('.smsva_wrapper').empty();
            }, 3000);
        }
    })
})
