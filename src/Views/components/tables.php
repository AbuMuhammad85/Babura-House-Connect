<?php
$headers = $headers ?? [];
?>
<div class="overflow-x-auto bg-white border border-slate-100 rounded-2xl shadow-sm">
    <table class="min-w-full divide-y divide-slate-100">
        <thead class="bg-slate-50">
            <tr>
                <?php foreach ($headers as $header): ?>
                    <th class="px-6 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <?= $header ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white text-xs text-slate-700">
            <?= $slot ?? '<!-- Rows dynamic content -->' ?>
        </tbody>
    </table>
</div>
