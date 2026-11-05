<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_log_in_without_a_500(): void
    {
        $campus = \App\Models\Campus::first();
        User::create(['name'=>'A','email'=>'a@test.com','password'=>bcrypt('secret123'),'role'=>'admin','is_admin'=>true,'is_active'=>true,'campus_id'=>$campus->id]);
        $r = $this->post('/login', ['email'=>'a@test.com','password'=>'secret123']);
        echo "\nstatus=".$r->getStatusCode().' location='.$r->headers->get('Location');
        if (isset($r->exception)) echo "\n".get_class($r->exception).': '.$r->exception->getMessage();
        $r->assertRedirect();
        $this->assertAuthenticated('web');
    }

    public function test_wrong_password_is_refused(): void
    {
        $campus = \App\Models\Campus::first();
        User::create(['name'=>'A','email'=>'a@test.com','password'=>bcrypt('secret123'),'role'=>'admin','is_admin'=>true,'is_active'=>true,'campus_id'=>$campus->id]);
        $this->post('/login', ['email'=>'a@test.com','password'=>'wrong'])->assertSessionHasErrors();
        $this->assertGuest('web');
    }
}
