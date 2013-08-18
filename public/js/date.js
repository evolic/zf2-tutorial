
function fixNumber(number, scale) {
	if (typeof(scale) === 'undefined') {
		scale = 2;
	}

	var n = number;
	var str = '';

	for (var i=scale; i>=1; i--) {
		var p = Math.pow(10, i-1);

		if (n >= p) {
			str += Math.floor(n / p);
		} else {
			str += '0';
		}

		n = n % p;
	}

	return str;
}

Date.prototype.getTimestamp = function() {
  var timestamp = this.getFullYear() + '-' + fixNumber(this.getMonth() + 1) + '-' + fixNumber(this.getDate());
  timestamp += ' ' + this.toTimeString().substring(0, 8);

  return timestamp;
};