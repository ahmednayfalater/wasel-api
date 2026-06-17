<!DOCTYPE html>
<html dir="rtl" lang="ar">
<body>
    <h2>تحديث حالة الدفع - وصل</h2>
    @if($status === 'approved')
        <p>تم قبول دفعتك بمبلغ {{ $amount }} ₪.</p>
    @else
        <p>تم رفض دفعتك بمبلغ {{ $amount }} ₪. يرجى إعادة رفع إثبات الدفع.</p>
    @endif
</body>
</html>
