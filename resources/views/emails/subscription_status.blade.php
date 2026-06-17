<!DOCTYPE html>
<html dir="rtl" lang="ar">
<body>
    <h2>تحديث حالة الاشتراك - وصل</h2>
    @if($status === 'active')
        <p>تم قبول اشتراكك في المولد {{ $generatorType }}.</p>
    @else
        <p>تم رفض اشتراكك في المولد {{ $generatorType }}.</p>
    @endif
</body>
</html>
