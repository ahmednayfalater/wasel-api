<!DOCTYPE html>
<html dir="rtl" lang="ar">
<body>
    <h2>فاتورة جديدة - وصل</h2>
    <p>لديك فاتورة جديدة بالتفاصيل التالية:</p>
    <ul>
        <li>المبلغ: {{ $invoice->amount }} ₪</li>
        <li>تاريخ الإصدار: {{ $invoice->release_date }}</li>
        <li>آخر موعد للدفع: {{ $invoice->due_date }}</li>
    </ul>
    <p>يرجى الدفع قبل تاريخ الاستحقاق.</p>
</body>
</html>
