import http from 'k6/http';
import {check, sleep} from 'k6';

export const options = {
    vus: 50,
    duration: '2m',
};

export default function () {
    const res = http.get(`${__ENV.BASE_URL || 'http://localhost:8080'}/api/category`);
    check(res, {'status 200': (r) => r.status === 200});
    sleep(0.1);
}
