export async function api(endpoint, { method = 'GET', data = null, headers = {} } = {}) {
    let url = '/supplier' + endpoint;
    const opts = { method, headers: { ...headers } };

    if (method === 'GET' && data) {
        url += '?' + new URLSearchParams(data);
    } else if (data instanceof FormData) {
        // NIE ustawiaj Content-Type, przeglądarka zrobi to sama
        opts.body = data;
    } else if (data) {
        opts.headers['Content-Type'] = 'application/json';
        opts.body = JSON.stringify(data);
    }

    const res = await fetch(url, opts);

    let payload = null;
    try {
        payload = await res.json();
    } catch (e) {}

    // `res` to Response - nie ma pola `success`, wiec dotad bylo tu undefined,
    // a wolajacy sprawdzaja `resp.success || resp.ok`. Przy odpowiedzi
    // HTTP 200 z {"success": false} `resp.ok` ratowalo warunek i blad API
    // pokazywal sie jako sukces (tak przechodzil nieudany import katalogu).
    // Odpowiedz jest udana tylko wtedy, gdy status jest 2xx ORAZ cialo nie
    // zglasza bledu.
    const apiReportedFailure = !!payload && payload.success === false;
    const succeeded = res.ok && !apiReportedFailure;

    // API zwraca powod pod `description` (czasem w `data.description`), a
    // widoki pokazuja `resp.message`, ktore bywa puste - dlatego kazdy blad
    // wygladal tak samo ("sprawdz format pliku"). Uzupelniamy `message`, gdy
    // jest puste, zeby prawdziwy powod dotarl do uzytkownika.
    const apiMessage =
        (payload && (payload.message
            || payload.description
            || (payload.data && payload.data.description))) || '';

    return {
        ...payload,
        message: apiMessage,
        ok:      succeeded,
        success: succeeded,
        status:  res.status,
        // Zachowane zachowanie: {"data": X} daje X, a goła tablica/obiekt
        // wraca w calosci (wczesniej wychodzilo to ze spreadu po `data`).
        data:    payload && payload.data !== undefined ? payload.data : payload,
    };
}

export async function downloadPdf(endpoint, payload = {}, defaultFilename = "plik.pdf", method = "GET") {
    try {

        let fetchOptions = {
            method: method.toUpperCase(),
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/pdf"
            },
            credentials: "same-origin"
        };

        let url = `/supplier${endpoint}`;

        if (fetchOptions.method === "GET") {
            const query = new URLSearchParams(payload).toString();
            url += "?" + query;
        } else {
            fetchOptions.body = JSON.stringify(payload);
        }

        const response = await fetch(url, fetchOptions);

        if (!response.ok) {
            let errorMessage = "";

            switch (response.status) {
                case 404:
                    errorMessage = "ENDPOINT_NOT_FOUND " + endpoint;
                    break;
                case 403:
                    errorMessage = "FORBIDDEN";
                    break;
                case 500:
                    errorMessage = "INTERNAL_SERVER_ERROR";
                    break;
                default:
                    errorMessage = `ERROR: ${response.status} ${response.statusText}`;
                    break;
            }

            const contentType = response.headers.get("Content-Type") || "";
            if (contentType.includes("application/json")) {
                const errorData = await response.json();
                if (errorData.message) {
                    // Jeśli `message` to tablica, to złącz
                    errorMessage = Array.isArray(errorData.message)
                        ? errorData.message.join(", ")
                        : errorData.message;
                }
            }

            throw new Error(errorMessage);
        }

        const blob = await response.blob();

        const disposition = response.headers.get("Content-Disposition") || "";
        const match = /filename="?(.+?)"?(;|$)/.exec(disposition);
        const filename = match ? match[1] : defaultFilename;

        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);

    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Błąd",
            text: err.message || "PDF_DOWNLOAD_ERROR"
        });
    }
}