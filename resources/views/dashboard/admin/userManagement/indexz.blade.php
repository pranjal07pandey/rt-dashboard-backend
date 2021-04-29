@include('dashboard.admin.include.header')
<div class="contentWrapper">
    <div class="container">
        <div class="row" style="padding-top: 15px;background: #fff;">
            <div class="col-md-4">
                <div class="sideMenu">
                    <div class="menuHeader">
                        <a href="{{ url('dashboard') }}" >
                            <i class="fa fa-th-list"></i> Docket Templates
                        </a>
                    </div>

                    <div class="menuHeader ">
                        <a href="#" class="active">
                            <i class="fa fa-user"></i> User Management <i class="fa fa-sort-down pull-right"></i>
                        </a>
                    </div>
                    <div class="menuContent">
                        <div class="form-group label-placeholder" style="margin-top:0px;">
                            <label for="i5p" class="control-label">Search</label>
                            <input type="email" class="form-control" id="i5p">
                        </div>

                        <div>
                            <button type="button" class="btn btn-raised btn-xs themeSecondaryBg">Contractors</button>
                            <button type="button" class="btn btn-raised btn-xs themeSecondaryBg">Admins</button>
                            <button type="button" class="btn btn-raised btn-xs pull-right themeSecondaryBg" >+</button>
                        </div>

                        <div class="docketBtnList">
                            <button type="button" class="btn btn-raised themePrimaryBg btn-block">John Doe <span class="pull-right">Admin</span></button>
                            <button type="button" class="btn btn-raised btn-block">John Doe <span class="pull-right">Admins</span></button>
                            <button type="button" class="btn btn-raised btn-block">John Doe <span class="pull-right">Admins</span></button>
                            <button type="button" class="btn btn-raised btn-block">John Doe <span class="pull-right">Admins</span></button>
                        </div>
                    </div>

                    <div class="menuHeader">
                        <a href="#">
                            <i class="fa fa-user"></i> Invoice Management
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="content" style="width:100%;min-height:500px;">
                    <div class="contentHeader">
                        <div class="horizontalList">
                            <span>Admin</span>
                            <span>Edit + Add Users  </span>
                            <div class="checkbox" style="display: inline-block;">
                                <label>
                                    <input id="ch1" type="checkbox" checked="">
                                </label>
                            </div>

                            <span>Backend Access  </span>
                            <div class="checkbox" style="display: inline-block;">
                                <label>
                                    <input id="ch1" type="checkbox" checked="">
                                </label>
                            </div>

                            <span>Edit + Add Users  </span>
                            <div class="checkbox" style="display: inline-block;">
                                <label>
                                    <input id="ch1" type="checkbox" checked="">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="min-height:200px;text-align: center;">
                        <div class="col-md-4">
                            <div class="profilePicContainer">
                                <i class="fa fa-user"></i>
                            </div>
                            <span style="text-align: center;display: block;margin-bottom:10px;">Add Profile Picture</span>
                            <a href="#" class="btn btn-primary themeSecondaryBg  subBtn withripple">Make Default</a>
                            <a href="#" class="btn btn-primary themeSecondaryBg  subBtn withripple">Reset to Default</a>
                            <br/><br/>
                        </div>
                        <div class="col-md-8" style="text-align: left;padding-top:20px;    padding-bottom: 20px;">
                            <div class="horizontalList">
                                <span>Status</span>
                                <span>Active  </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </div>
                            <div class="horizontalList">
                                <span>Username</span>
                                <div class="form-group">
                                    <input id="username" type="text" class="form-control" name="username" placeholder="john.name@hotmail.com" value="{{ old('username') }}" required autofocus>
                                </div>
                            </div>
                            <div class="horizontalList">
                                <span>Password</span>
                                <div class="form-group">
                                    <input id="password" type="text" class="form-control" name="password" placeholder="XXXXXXXXXX" value="{{ old('password') }}" required autofocus>
                                </div>
                            </div>

                            <div class="horizontalList">
                                <span>First Name</span>
                                <div class="form-group">
                                    <input id="firstName" type="text" class="form-control" name="firstName" placeholder="First Name" value="{{ old('firstName') }}" required autofocus>
                                </div>
                            </div>
                            <div class="horizontalList">
                                <span>Last Name</span>
                                <div class="form-group">
                                    <input id="lastName" type="text" class="form-control" name="lastName" placeholder="Last Name" value="{{ old('lastName') }}" required autofocus>
                                </div>
                            </div>
                            <br/>
                        </div>
                    </div>

                    <table class="horizontalMenuList">
                        <tr>
                            <td>
                                <span>Dockets</span>
                            </td>
                            <td>
                                <span>Send  </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Receive   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Edit Templates   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Approve   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Invoices</span>
                            </td>
                            <td>

                                <span>Send  </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Receive   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Edit Templates   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Approve   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>App</span>
                            </td>
                            <td>
                                <span>Check in-out </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Dockets   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>EInvoices   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Manager   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Apply to:</span>
                            </td>
                            <td>
                                <span>Admin  </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Manager   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span>Contractor   </span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="form-group label-placeholder" style="margin-top:0px;">
                                    <label for="i5p" class="control-label">Search</label>
                                    <input type="email" class="form-control" id="i5p">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('dashboard.admin.include.footer')