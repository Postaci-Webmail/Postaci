'use strict';

can.stache.registerHelper('linkTo', function(page){
	return can.stache.safeString( can.route.link(page,{page:page}) );
});

can.stache.registerHelper('hrefTo', function(page){
	return can.stache.safeString(can.route.url({page: page}));
});

can.stache.registerHelper('money', function(amount){
	if (typeof amount()) {
		return accounting.formatMoney(amount()/100);
	} else {
		return '';
	}
});

can.stache.registerHelper('number', function(amount, decimals){
	var decimals = decimals || 0;
	if (typeof amount()) {
		return accounting.formatMoney(amount(), '', decimals);
	} else {
		return '';
	}
});

// If the passed amount < 0, returns the word 'negative'.
can.stache.registerHelper('depositOrWithdrawal', function(amount){
	if (amount() < 0) {
		return 'withdrawal';
	} else {
		return 'deposit';
	}
});

// Returns 'black' or 'red' class depending on if positive or negative.
can.stache.registerHelper('blackOrRed', function(balance){
	if (balance() < 0) {
		return 'red';
	} else {
		return 'black';
	}
});

can.stache.registerHelper('shortPrettyDate', function(date){
	if (date()) {
		var d = moment(date());
		var now = moment();
		if (d.year() === now.year()) {
			return d.format('MMM D');
		} else {
			return d.format('MMM D, YYYY');
		}
	} else {
		return '';
	}
});
