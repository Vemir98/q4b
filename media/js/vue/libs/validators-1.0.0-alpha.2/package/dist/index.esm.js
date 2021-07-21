import { isRef } from '@vue/composition-api';

function isFunction(val) {
  return typeof val === 'function';
}
/**
 * Unwraps a ref, returning its value
 * @param val
 * @return {*}
 */

function unwrap(val) {
  return isRef(val) ? val.value : val;
}
/**
 * @typedef ValidatorObject
 * @property {Function} $validator
 * @property {Function|String} $message
 * @property {Object|Array} $params
 */

/**
 * Returns a standard ValidatorObject
 * Wraps a plain function into a ValidatorObject
 * @param {ValidatorObject|Function} validator
 * @return {ValidatorObject}
 */

function getValidatorObj(validator) {
  return isFunction(validator.$validator) ? validator : {
    $validator: validator
  };
}

/**
 * Allows attaching parameters to a validator
 * @param {Object} $params
 * @param {ValidatorObject|Function} $validator
 * @return {ValidatorObject}
 */

function withParams($params, $validator) {
  var validatorObj = getValidatorObj($validator);
  validatorObj.$params = Object.assign({}, validatorObj.$params, {}, $params);
  return validatorObj;
}

/**
 * @callback MessageCallback
 * @param {Object} params
 * @return String
 */

/**
 * Attaches a message to a validator
 * @param {Function | Object} $validator
 * @param {(MessageCallback | String)} $message
 */

function withMessage($validator, $message) {
  var validatorObj = getValidatorObj($validator);
  validatorObj.$message = $message;
  return validatorObj;
}

function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

// "required" core, used in almost every validator to allow empty values
var req = function req(value) {
  if (Array.isArray(value)) return !!value.length;

  if (value === undefined || value === null) {
    return false;
  }

  if (value === false) {
    return true;
  }

  if (value instanceof Date) {
    // invalid date won't pass
    return !isNaN(value.getTime());
  }

  if (_typeof(value) === 'object') {
    for (var _ in value) {
      return true;
    }

    return false;
  }

  return !!String(value).length;
};
/**
 * Returns the length of an arbitrary value
 * @param {Array|Object|String} value
 * @return {number}
 */

var len = function len(value) {
  if (Array.isArray(value)) return value.length;

  if (_typeof(value) === 'object') {
    return Object.keys(value).length;
  }

  return String(value).length;
};
/**
 * Regex based validator template
 * @param {RegExp} expr
 * @return {function(*=): boolean}
 */

var regex = function regex(expr) {
  return function (value) {
    return !req(value) || expr.test(value);
  };
};



var common = /*#__PURE__*/Object.freeze({
  __proto__: null,
  withParams: withParams,
  withMessage: withMessage,
  req: req,
  len: len,
  regex: regex
});

var alpha = regex(/^[a-zA-Z]*$/);

var alpha$1 = {
  $validator: alpha,
  $message: 'The value is not alphabetical'
};

var alphaNum = regex(/^[a-zA-Z0-9]*$/);

var alphaNum$1 = {
  $validator: alphaNum,
  $message: 'The value must be alpha-numeric'
};

var numeric = regex(/^[0-9]*$/);

var numeric$1 = {
  $validator: numeric,
  $message: 'Value must be numeric'
};

var between = (function (min, max) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +unwrap(min) <= +value && +unwrap(max) >= +value;
  };
});

var between$1 = (function (min, max) {
  return {
    $validator: between(min, max),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "The value must be between ".concat($params.min, " and ").concat($params.max);
    },
    $params: {
      min: min,
      max: max
    }
  };
});

var emailRegex = /(^$|^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)/;
var email = regex(emailRegex);

var email$1 = {
  $validator: email,
  $message: 'Value is not a valid email address'
};

var ipAddress = (function (value) {
  if (!req(value)) {
    return true;
  }

  if (typeof value !== 'string') {
    return false;
  }

  var nibbles = value.split('.');
  return nibbles.length === 4 && nibbles.every(nibbleValid);
});

var nibbleValid = function nibbleValid(nibble) {
  if (nibble.length > 3 || nibble.length === 0) {
    return false;
  }

  if (nibble[0] === '0' && nibble !== '0') {
    return false;
  }

  if (!nibble.match(/^\d+$/)) {
    return false;
  }

  var numeric = +nibble | 0;
  return numeric >= 0 && numeric <= 255;
};

var ipAddress$1 = {
  $validator: ipAddress,
  $message: 'The value is not a valid IP address'
};

var macAddress = (function () {
  var separator = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : ':';
  return function (value) {
    separator = unwrap(separator);

    if (!req(value)) {
      return true;
    }

    if (typeof value !== 'string') {
      return false;
    }

    var parts = typeof separator === 'string' && separator !== '' ? value.split(separator) : value.length === 12 || value.length === 16 ? value.match(/.{2}/g) : null;
    return parts !== null && (parts.length === 6 || parts.length === 8) && parts.every(hexValid);
  };
});

var hexValid = function hexValid(hex) {
  return hex.toLowerCase().match(/^[0-9a-f]{2}$/);
};

var macAddress$1 = (function (separator) {
  return {
    $validator: macAddress(separator),
    $message: 'The value is not a valid MAC Address'
  };
});

var maxLength = (function (length) {
  return function (value) {
    return !req(value) || len(value) <= unwrap(length);
  };
});

var maxLength$1 = (function (max) {
  return {
    $validator: maxLength(max),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "The maximum length allowed is ".concat($params.max);
    },
    $params: {
      max: max
    }
  };
}); // Still figuring out which is less error prone
// export default (max) => withMessage(
//   withParams({ max }, maxLength),
//   ({ $params }) => `The maximum length allowed is ${$params.max}`
// )

var minLength = (function (length) {
  return function (value) {
    return !req(value) || len(value) >= unwrap(length);
  };
});

var minLength$1 = (function (length) {
  return {
    $validator: minLength(length),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "This field should be at least ".concat($params.length, " long.");
    },
    $params: {
      length: length
    }
  };
});

var required = (function (value) {
  if (typeof value === 'string') {
    return req(value.trim());
  }

  return req(value);
});

var required$1 = {
  $validator: required,
  $message: 'Value is required'
};

var requiredIf = (function (prop) {
  return function (value) {
    return req(prop) ? req(value) : true;
  };
});

var requiredIf$1 = (function (prop) {
  return {
    $validator: requiredIf(prop),
    $message: 'The value is required'
  };
});

var requiredUnless = (function (prop) {
  return function (value) {
    return !req(prop) ? req(value) : true;
  };
});

var requiredUnless$1 = (function (prop) {
  return {
    $validator: requiredUnless(prop),
    $message: 'The value is required'
  };
});

var sameAs = (function (equalTo) {
  return function (value) {
    return value === unwrap(equalTo);
  };
});

var sameAs$1 = (function (equalTo, otherName) {
  return {
    $validator: sameAs(equalTo),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "The value must be equal to the ".concat(otherName, " value.");
    },
    $params: {
      equalTo: equalTo,
      otherName: otherName
    }
  };
});

var urlRegex = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i;
var url = regex(urlRegex);

var url$1 = {
  $validator: url,
  $message: 'The value is not a valid URL address'
};

var or = (function () {
  for (var _len = arguments.length, validators = new Array(_len), _key = 0; _key < _len; _key++) {
    validators[_key] = arguments[_key];
  }

  return function () {
    var _this = this;

    for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }

    return validators.length > 0 && validators.reduce(function (valid, fn) {
      return valid || fn.apply(_this, args);
    }, false);
  };
});

var or$1 = (function () {
  return {
    $validator: or.apply(void 0, arguments),
    $message: 'The value does not match any of the provided validators'
  };
});

var and = (function () {
  for (var _len = arguments.length, validators = new Array(_len), _key = 0; _key < _len; _key++) {
    validators[_key] = arguments[_key];
  }

  return function () {
    var _this = this;

    for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }

    return validators.length > 0 && validators.reduce(function (valid, fn) {
      return valid && fn.apply(_this, args);
    }, true);
  };
});

var and$1 = (function () {
  return {
    $validator: and.apply(void 0, arguments),
    $message: 'The value does not match all of the provided validators'
  };
});

var not = (function (validator) {
  return function (value, vm) {
    return !req(value) || !validator.call(this, value, vm);
  };
});

var not$1 = (function (validator) {
  return {
    $validator: not(validator),
    $message: "The value does not match the provided validator"
  };
});

var minValue = (function (min) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +value >= +unwrap(min);
  };
});

var minValue$1 = (function (min) {
  return {
    $validator: minValue(min),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "The minimum value allowed is ".concat($params.min);
    },
    $params: {
      min: min
    }
  };
});

var maxValue = (function (max) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +value <= +unwrap(max);
  };
});

var maxValue$1 = (function (max) {
  return {
    $validator: maxValue(max),
    $message: function $message(_ref) {
      var $params = _ref.$params;
      return "The maximum value is ".concat($params.max);
    },
    $params: {
      max: max
    }
  };
});

// ^-[0-9]+$ - only for negative integer (minus sign without at least 1 digit is not a number)

var integer = regex(/(^[0-9]*$)|(^-[0-9]+$)/);

var integer$1 = {
  $validator: integer,
  $message: 'Value is not an integer'
};

var decimal = regex(/^[-]?\d*(\.\d+)?$/);

var decimal$1 = {
  $validator: decimal,
  $message: 'Value must be decimal'
};

export { alpha$1 as alpha, alphaNum$1 as alphaNum, and$1 as and, between$1 as between, decimal$1 as decimal, email$1 as email, common as helpers, integer$1 as integer, ipAddress$1 as ipAddress, macAddress$1 as macAddress, maxLength$1 as maxLength, maxValue$1 as maxValue, minLength$1 as minLength, minValue$1 as minValue, not$1 as not, numeric$1 as numeric, or$1 as or, required$1 as required, requiredIf$1 as requiredIf, requiredUnless$1 as requiredUnless, sameAs$1 as sameAs, url$1 as url };
