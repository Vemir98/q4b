'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

var compositionApi = require('@vue/composition-api');

function isFunction(val) {
  return typeof val === 'function';
}
/**
 * Unwraps a ref, returning its value
 * @param val
 * @return {*}
 */

function unwrap(val) {
  return compositionApi.isRef(val) ? val.value : val;
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

var alphaNum = regex(/^[a-zA-Z0-9]*$/);

var numeric = regex(/^[0-9]*$/);

var between = (function (min, max) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +unwrap(min) <= +value && +unwrap(max) >= +value;
  };
});

var emailRegex = /(^$|^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$)/;
var email = regex(emailRegex);

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

var maxLength = (function (length) {
  return function (value) {
    return !req(value) || len(value) <= unwrap(length);
  };
});

var minLength = (function (length) {
  return function (value) {
    return !req(value) || len(value) >= unwrap(length);
  };
});

var required = (function (value) {
  if (typeof value === 'string') {
    return req(value.trim());
  }

  return req(value);
});

var requiredIf = (function (prop) {
  return function (value) {
    return req(prop) ? req(value) : true;
  };
});

var requiredUnless = (function (prop) {
  return function (value) {
    return !req(prop) ? req(value) : true;
  };
});

var sameAs = (function (equalTo) {
  return function (value) {
    return value === unwrap(equalTo);
  };
});

var urlRegex = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i;
var url = regex(urlRegex);

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

var not = (function (validator) {
  return function (value, vm) {
    return !req(value) || !validator.call(this, value, vm);
  };
});

var minValue = (function (min) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +value >= +unwrap(min);
  };
});

var maxValue = (function (max) {
  return function (value) {
    return !req(value) || (!/\s/.test(value) || value instanceof Date) && +value <= +unwrap(max);
  };
});

// ^-[0-9]+$ - only for negative integer (minus sign without at least 1 digit is not a number)

var integer = regex(/(^[0-9]*$)|(^-[0-9]+$)/);

var decimal = regex(/^[-]?\d*(\.\d+)?$/);

exports.alpha = alpha;
exports.alphaNum = alphaNum;
exports.and = and;
exports.between = between;
exports.decimal = decimal;
exports.email = email;
exports.helpers = common;
exports.integer = integer;
exports.ipAddress = ipAddress;
exports.macAddress = macAddress;
exports.maxLength = maxLength;
exports.maxValue = maxValue;
exports.minLength = minLength;
exports.minValue = minValue;
exports.not = not;
exports.numeric = numeric;
exports.or = or;
exports.required = required;
exports.requiredIf = requiredIf;
exports.requiredUnless = requiredUnless;
exports.sameAs = sameAs;
exports.url = url;
