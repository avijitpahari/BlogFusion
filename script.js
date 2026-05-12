(async function () {
    let res = await fetch("https://dummyjson.com/products");
    let AllData = await res.json();
    let products = AllData.products;
    console.log(AllData);
    const cardContainer = document.getElementById("card");

    products.forEach((product) => {
        cardContainer.innerHTML += `
        <div class="col-md-12 col-lg-4 mb-4">
            <div class="card" onclick="window.location.href='test.html?id=${product.id}'">
                <div class="d-flex justify-content-between p-3">
                    <p class="lead mb-0">Special Offer</p>
                    <div class="bg-info rounded-circle d-flex align-items-center justify-content-center shadow-1-strong"
                        style="width: 35px; height: 35px;">
                        <p class="text-white mb-0 small">-${Math.round(product.discountPercentage)}%</p>
                    </div>
                </div>
                <img src="${product.thumbnail}" width="100" height="250" class="card-img-top" alt="${product.title}"  />
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <p class="small"><a href="#!" class="text-muted">${product.category}</a></p>
                        <p class="small text-danger"><s>₹${(product.price + product.price*(product.discountPercentage/100)).toFixed(2)}</s></p>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="mb-0">${product.title}</h5>
                        <h5 class="text-dark mb-0">₹${product.price}</h5>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <p class="text-muted mb-0">Available: <span class="fw-bold">${product.stock}</span></p>
                        <div class="ms-auto text-warning">
                            ${product.rating} <i class="fa fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    });
          
})();
