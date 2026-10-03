const https = require('https');

function fetchJson(url) {
    return new Promise((resolve, reject) => {
        https.get(url, { headers: { 'User-Agent': 'EBUS-Test' } }, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve(JSON.parse(data)));
        }).on('error', reject);
    });
}

async function test() {
    let d1 = await fetchJson('https://nominatim.openstreetmap.org/search?format=json&q=Ahmedabad&countrycodes=in&limit=1');
    let p1 = d1[0];
    
    let d2 = await fetchJson('https://nominatim.openstreetmap.org/search?format=json&q=Surat&countrycodes=in&limit=1');
    let p2 = d2[0];
    
    console.log("Ahmedabad:", p1.lat, p1.lon);
    console.log("Surat:", p2.lat, p2.lon);
    
    let url = `https://router.project-osrm.org/route/v1/driving/${p1.lon},${p1.lat};${p2.lon},${p2.lat}?overview=false`;
    console.log("OSRM URL:", url);
    let d3 = await fetchJson(url);
    console.log("Distance (km):", d3.routes[0].distance / 1000);
}

test();
