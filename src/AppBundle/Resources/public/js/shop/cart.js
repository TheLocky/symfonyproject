var cart = null;

$(document).ready(function () {
	cart = Cookies.get("cart");
	//if (!cartInfo) { cartInfo = '{ "items": [] }'; }
	cart = JSON.parse(cart);
	//updateCart();
});

function updateCart(cookiesUpdateOnly) {
	if (cookiesUpdateOnly === false) {
		var fullCount = 0;
		var fullCost = 0;
		for (var i = 0; i < cart.items.length; i++) {
			var item = cart.items[i];
			fullCount += parseInt(item.count);
			fullCost += item.cost * item.count;
		}	
		$("#itemsCount").html('Товаров: ' + fullCount);
		$("#cost").html('На сумму: $' + fullCost.toFixed(2));
	}
	Cookies.set("cart", cart, {expires: 1});
}

function addItemToCart(_id, _cost) {
	var exists = $.grep(cart.items, function(e){ return e.id == _id; });
	if (exists.length == 0) {
		cart.items.push( { id: _id, cost: _cost, count: 1 } );
	} else if(exists.length == 1) {
		exists[0].count++;
	} else {
		var exists = exists[0];
		cart.items = cart.items.filter(function(e) {e.id !== _id});
		exists.count++;
		cart.items.push(exists);
	}

	updateCart(false);
}

function clearCart() {
	cart = { items: [] };
	updateCart(false);
}

function itemCountChange(index)
{
	var item = cart.items[index];
	var newCount = $('#item-' + item.id + '-count').val();
	item.count = newCount;
	$('#item-' + item.id + '-cost').html('Сумма: $' + (item.count * item.cost).toFixed(2));
	updateCart(true);
	updateFullCost();
}

function itemRemove(index)
{
	$('#item-' + cart.items[index].id).remove();
	cart.items.splice(index,1);
	updateCart(false);
	if (cart.items.length == 0)
	{
		$('#cart-body').visible(false);
		window.location.href = "/shop";
	} else {
		updateFullCost();
	}
}

function updateFullCost() {
	var fullCost = 0;
	for (var i = 0; i < cart.items.length; i++) {
		fullCost += cart.items[i].cost * cart.items[i].count;
	}
	$('#full-cost').html('Итого: $' + fullCost.toFixed(2));
}

function doBuy() {
	$('#buyerror').hide();
	$.ajax({
		url: '/shop/api/v1?cmd=buy',
		dataType: 'json',
		async: false,
		success: function(data) {
			if (data.success) {
				cart = { items: [] };
				updateCart(true);
				window.location.href = '/shop/cart/success';
			} else {
				showError(data.error);
			}
		},
		error: function(data, text, thrown) {
			showError('Неизвестная ошибка');
			console.log(data);
			console.log(text);
			console.log(thrown);
		}
	});
}

function showError(error)
{
	$('#buyerror').show();
	$('#buyerror').html(error);
	window.scrollTo(0,0);
}