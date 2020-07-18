//下面有为空的验证了，所以这里不用考虑
$.validator.addMethod('validatePassword',
    function(value, element, param) {
        if (value != '') {
            if (value.match('/.*[a-z]+.*/i') == null) {
                return false;
            }
            if (value.match('/.*[0-9]+.*/') == null) {
                return false;
            }
        }
        return true;
    },
    'Must contain at least one letter and one number'
    );