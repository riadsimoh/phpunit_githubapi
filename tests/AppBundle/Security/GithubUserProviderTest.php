<?php


namespace Tests\AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Security\GithubUserProvider;
use Doctrine\Bundle\DoctrineBundle\Tests\TestCase;


class GithubUserProviderTest extends TestCase {
private $client;
private $serializer;
private $streamInterface;
private $response;


  public function setup() {

    $this->client = $this->getMockBuilder('GuzzleHttp\Client')
                         ->disableOriginalConstructor()
                         ->setMethods(['get'])
                         ->getMock();

    $this->serializer = $this->getMockBuilder('JMS\Serializer\Serializer')
                             ->disableOriginalConstructor()
                             ->getMock(); 
    $this->streamInterface = $this->getMockBuilder("Psr\Http\Message\StreamInterface")
                                  ->disableOriginalConstructor()
                                  ->getMock();
                    
     $this->response = $this->getMockBuilder("Psr\Http\Message\RequestInterface")
                            ->disableOriginalConstructor()
                            ->getMock(); 
                      


  }


  public  function testLoadUserReturnAUserObject()
  {

     $this->response->expects($this->once())->method("getBody")->willReturn($this->streamInterface);

     $this->client->expects($this->once())->method('get')->willReturn($this->response);  

     $userData = ['login' => 'a login', 'name' => 'user name', 'email' => 'adress@mail.com', 'avatar_url' => 'url to the avatar', 'html_url' => 'url to profile'];
     
     $this->serializer->expects($this->once())->method("deserialize")->willReturn($userData);
      
    $gitHubUserProvider = new GithubUserProvider($this->client, $this->serializer);

    $user = $gitHubUserProvider->loadUserByUsername("riad");


    $expectedUser = new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);
        
        $this->assertEquals($expectedUser, $user);
        $this->assertEquals('AppBundle\Entity\User', get_class($user));
    }




    public function testeLoadUserReturnNull() {

      $this->client->expects($this->once())->method("get")->willReturn($this->response);
      $this->response->expects($this->once())->method("getBody")->willReturn($this->streamInterface);

      $this->serializer->expects($this->once())->method("deserialize")->willReturn([]);

      $this->expectException('LogicException');
      $githubUserPorvider =  new GithubUserProvider($this->client, $this->serializer);

       $user = $githubUserPorvider->loadUserByUsername("riad");
    }

    public function tearDown()
    {
        $this->client = null;
        $this->serializer = null;
        $this->streamedResponse = null;
        $this->response = null;
    }
   
  }










