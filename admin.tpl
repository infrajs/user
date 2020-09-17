{list:}
    {:hat}
    {listbody:}
    <style>
        .table_sort thead td,
        .table_sort thead th {
            cursor: pointer;
        }
        th.sorted[data-order="1"],
        th.sorted[data-order="-1"] {
            position: relative;
        }

        th.sorted[data-order="1"]::after,
        th.sorted[data-order="-1"]::after {
            right: 8px;
            position: absolute;
        }

        th.sorted[data-order="-1"]::after {
            content: "▼"
        }

        th.sorted[data-order="1"]::after {
            content: "▲"
        }
    </style>
    <table class="table_sort table table-sm table-striped">
        <thead class="thead-light">
            <tr class="success">
                <th>{User.lang(:Email)}</th>
                <th>{User.lang(:Password)}</th>
                <th>{User.lang(:City)}</th>
                <th>{User.lang(:Activity)}</th>
            </tr>
        </thead>
        <tbody>
            {data.list::user}
        </tbody>
    </table>
    <script class="module">
        const getSort = ({ target }) => {
            const order = (target.dataset.order = -(target.dataset.order || -1))
            const index = [...target.parentNode.cells].indexOf(target)
            const collator = new Intl.Collator(['en', 'ru'], { 
                numeric: true 
            })
            const comparator = (index, order) => (a, b) => order * collator.compare(
                a.children[index].innerHTML,
                b.children[index].innerHTML
            )
            const tBody = target.closest('table').tBodies[0]
            tBody.append(...[...tBody.rows].sort(comparator(index, order)))

            for (const cell of target.parentNode.cells) {
                cell.classList.toggle('sorted', cell === target)
            }
        }
        
        const div = document.getElementById('{div}')
        const thead = div.getElementsByTagName('thead')[0]
        thead.addEventListener('click', getSort)

    </script>
    {user:}
        <tr>
            <td>{email}</td>
            <td title="{user_id}-{token}">{password}</td>
            <td>{city}</td>
            <td>{~date(:Y/m/d H:i,dateactive)}</td>
        </tr>
{hat:}
	<h1>{User.lang(title)}</h1>
	{config.ans.msg?config.ans.msg:alert}
    {config.ans.result??:datamsg}
    {datamsg:}
        {data.msg?data.msg:alert?:{tplroot}body}
    {alert:}
        <div style="margin-top:20px;" class="alert alert-{..result?:success?:danger}">
            {.}
        </div>