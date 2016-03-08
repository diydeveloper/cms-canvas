<div class="box">
    <div class="heading">
    </div>
    <div id="profile" class="content">
        <div id="profile_header">
            <img src="{!! $user->avatar('100', 100) !!}" />
            <h2>{{ $user->getFullName() }}</h2>
        </div>
            <table>
                <tr>
                    <td class="field">First Name:</td>
                    <td>{{ $user->first_name }}</td>
                </tr>
                <tr>
                    <td class="field">Last Name:</td>
                    <td>{{ $user->last_name }}</td>
                </tr>

                <tr>
                    <td class="field">Phone:</td>
                    <td>{{ $user->phone }}</td>
                </tr>

                <tr>
                    <td class="field">Address:</td>
                    <td>{{ $user->address }}</td>
                </tr>

                <tr>
                    <td class="field">Address 2:</td>
                    <td>{{ $user->address2 }}</td>
                </tr>

                <tr>
                    <td class="field">City:</td>
                    <td>{{ $user->city }}</td>
                </tr>

                <tr>
                    <td class="field">State / Province:</td>
                    <td>{{ $user->state }}</td>
                </tr>

                <tr>
                    <td class="field">Country:</td>
                    <td>{{ $user->country }}</td>
                </tr>

                <tr class="no_border">
                    <td class="field">Zip:</td>
                    <td>{{ $user->zip }}</td>
                </tr>
            </table>
    </div>
</div>