<?php

use App\Helpers\Calculate;

test('Testing roundDown function', function () {
    expect(Calculate::roundDown(2134.1234567, 4))->toEqual(2134.1234);

    expect(Calculate::roundDown(2134.7898789, 4))->toEqual(2134.7898);

    expect(Calculate::roundDown(2134.9999999, 2))->toEqual(2134.99);
});
