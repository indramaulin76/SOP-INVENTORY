## 2025-12-16 - Dashboard Optimization
**Learning:** Grouping by date in database queries (`groupBy('tanggal')`) is significantly faster than iterating through dates in PHP and executing queries for each day.
**Action:** Whenever building charts or reports that aggregate data over time, always try to fetch the aggregated data in a single query (or one per table) using grouping, rather than looping in the application layer.
