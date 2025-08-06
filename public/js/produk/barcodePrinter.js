const BarcodePrinter = {
    print(barcode, name = "Produk") {
        if (!barcode) {
            showAlert("error", "Barcode tidak tersedia");
            return;
        }

        const canvas = document.createElement("canvas");
        JsBarcode(canvas, barcode, {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: true,
            fontSize: 14
        });

        const imgData = canvas.toDataURL("image/png");

        const printWindow = window.open("", "_blank");
        printWindow.document.write(`
            <html>
                <head>
                    <title>Cetak Barcode</title>
                    <style>
                        body { text-align: center; font-family: sans-serif; }
                        img { margin-top: 20px; }
                        h2 { margin-top: 10px; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <h2>${name}</h2>
                    <img src="${imgData}" alt="Barcode" />
                </body>
            </html>
        `);

        printWindow.document.close();
    }
};

const showAlert = (type, message) => {
    const alertId = `alert-${Date.now()}`;
    const alertConfig = {
        success: {
            bgColor: "bg-orange-50",
            borderColor: "border-orange-200",
            textColor: "text-orange-800",
            icon: "check-circle",
            iconColor: "text-orange-500",
        },
        error: {
            bgColor: "bg-red-50",
            borderColor: "border-red-200",
            textColor: "text-red-800",
            icon: "alert-circle",
            iconColor: "text-red-500",
        },
    };

    const config = alertConfig[type] || alertConfig.success;

    const alertElement = document.createElement("div");
    alertElement.id = alertId;
    alertElement.className = `p-4 border rounded-lg shadow-sm ${config.bgColor} ${config.borderColor} ${config.textColor} flex items-start gap-3 animate-fade-in-up`;
    alertElement.innerHTML = `
        <i data-lucide="${config.icon}" class="w-5 h-5 mt-0.5 ${config.iconColor}"></i>
        <div class="flex-1">
            <p class="text-sm font-medium">${message}</p>
        </div>
        <button onclick="closeAlert('${alertId}')" class="p-1 rounded-full hover:bg-gray-100">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    `;

    alertContainer.prepend(alertElement);
    if (window.lucide) window.lucide.createIcons();

    // Auto-close after 2 seconds
    setTimeout(() => closeAlert(alertId), 1300);
};

const closeAlert = (id) => {
    const alert = document.getElementById(id);
    if (alert) {
        alert.classList.add("opacity-0", "transition-opacity", "duration-800");
        setTimeout(() => alert.remove(), 800);
    }
};

// Ekspos ke global
window.BarcodePrinter = BarcodePrinter;
window.closeAlert = closeAlert;
