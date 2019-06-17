<?php

namespace App\Http\Controllers\Api;

use DB,
    Storage;
use App\User;
use Exception;
use App\Http\Requests\Api\{
    ConfigurationRequest,
    ConfigurationSoftwareRequest,
    ConfigurationCertificateRequest
};
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="api_token",
 *     name="Authorization"
 * ),
 * @OA\OpenApi(
 *     @OA\Server(
 *         url="http://127.0.0.1:8000",
 *         description="Server"
 *     ),
 *     @OA\Info(
 *         version="0.1",
 *         title="API Facturación Electronica Validación Previa DIAN",
 *         description="Documentación API Facturación Electronica Validación Previa DIAN, [Listados de códigos](/listings)."
 *     )
 * ),
 * @OA\Tag(
 *     name="Configuración",
 *     description="Configuración inicial del API."
 * ),
 */
class ConfigurationController extends Controller
{
    // Bearer nwflpzMsyCiYI6pca5jIj6Zh4jsoMwFkUArT55IvfYGFHOcef5oyzfAxq3YwXvGaaWOHS8aa2hVjaf0i
    /**
     * @OA\Post(
     *    tags={"Configuración"},
     *    path="/api/v2.1/config/{nit}/{dv}",
     *    summary="Datos de la empresa",
     *    security={{"api_token":{}}},
     *    @OA\RequestBody(
     *        required=true,
     *        description="Objeto para la creación de la empresa.",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                required={
     *                  "type_document_identification_id",
     *                  "type_organization_id",
     *                  "type_regime_id",
     *                  "type_liability_id",
     *                  "business_name",
     *                  "municipality_id",
     *                  "address",
     *                  "phone",
     *                  "email"
     *                },
     *                @OA\Property(
     *                    property="type_environment_id",
     *                    description="Código del ambiente",
     *                    type="integer",
     *                    default=2
     *                ),
     *                @OA\Property(
     *                    property="type_document_identification_id",
     *                    description="Código del tipo identificación de documento",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="country_id",
     *                    description="Código del pais",
     *                    type="integer",
     *                    default=46,
     *                ),
     *                @OA\Property(
     *                    property="type_currency_id",
     *                    description="Código del tipo moneda por defecto",
     *                    type="integer",
     *                    default="35"
     *                ),
     *                @OA\Property(
     *                    property="type_organization_id",
     *                    description="Código del tipo organización",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="type_regime_id",
     *                    description="Código del tipo regimen",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="type_liability_id",
     *                    description="Código del tipo responsabilidad",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="business_name",
     *                    description="Razón social",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="municipality_id",
     *                    description="Código del municipio",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="address",
     *                    description="Dirección",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="phone",
     *                    description="Teléfono",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="email",
     *                    description="Correo electrónico",
     *                    type="string",
     *                    format="email"
     *                ),
     *                example={
     *                    "type_document_identification_id": 6,
     *                    "type_organization_id": 1,
     *                    "type_regime_id": 2,
     *                    "type_liability_id": 19,
     *                    "business_name": "EMPRESA DE PRUEBAS",
     *                    "municipality_id": 1006,
     *                    "address": "CALLE 1 1C 1",
     *                    "phone": 3216547,
     *                    "email": "test@test.test"
     *                }
     *            )
     *        )
     *    ),
     *    @OA\Parameter(
     *         name="nit",
     *         description="Número de Identificación Tributaria RUT",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *    ),
     *    @OA\Parameter(
     *         name="dv",
     *         description="Dígito de verificación RUT",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="OK",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unprocessable Entity",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Bad Request",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Not Found",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=500,
     *        description="Internal Server Error",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    )
     * )
     */
    public function store(ConfigurationRequest $request, $nit, $dv = null) {
        DB::beginTransaction();
        
        try {
            $password = Str::random(80);
            
            $user = User::create([
                'name' => $request->business_name,
                'email' => $request->email,
                'password' => bcrypt($password)
            ]);
            
            $user->api_token = hash('sha256', $password);
            
            $user->company()->create([
                'user_id' => $user->id,
                'identification_number' => $nit,
                'dv' => $dv,
                'type_environment_id' => $request->type_environment_id ?? 2,
                'type_document_identification_id' => $request->type_document_identification_id,
                'country_id' => $request->country_id ?? 46,
                'type_currency_id' => $request->type_currency_id ?? 35,
                'type_organization_id' => $request->type_organization_id,
                'type_regime_id' => $request->type_regime_id,
                'type_liability_id' => $request->type_liability_id,
                'municipality_id' => $request->municipality_id,
                'address' => $request->address,
                'phone' => $request->phone
            ]);
            
            $user->save();
            
            DB::commit();
            
            return [
                'message' => 'Empresa creada con éxito',
                'password' => $password,
                'token' => $password,
                'company' => $user->company
            ];
        }
        catch (Exception $e) {
            DB::rollBack();
            
            return response([
                'message' => 'Internal Server Error',
                'payload' => $e->getMessage()
            ], 500);
        }
    }
    
    // Bearer xPlPAE1AmsM4xVoijA8C7nw2lfSPC1lIuO8w9OhD8JC4xFEHGknn3KTtvFtngquLrFcSVoFtw0oPiMrC
    /**
     * @OA\Put(
     *    tags={"Configuración"},
     *    path="/api/v2.1/config/software",
     *    summary="Datos del software",
     *    security={{"api_token":{}}},
     *    @OA\RequestBody(
     *        required=true,
     *        description="Objeto para la configuración del software.",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                required={
     *                  "id",
     *                  "pin"
     *                },
     *                @OA\Property(
     *                    property="id",
     *                    description="Id del software",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="pin",
     *                    description="Pin del software",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="url",
     *                    description="URL del software",
     *                    type="string",
     *                    default="https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc"
     *                ),
     *                example={
     *                    "id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
     *                    "pin": 12345
     *                }
     *            )
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="OK",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unprocessable Entity",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Bad Request",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Not Found",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=500,
     *        description="Internal Server Error",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    )
     * )
     */
    public function storeSoftware(ConfigurationSoftwareRequest $request) {
        DB::beginTransaction();
        
        try {
            auth()->user()->company->software()->delete();
            
            $software = auth()->user()->company->software()->create([
                'identifier' => $request->id,
                'pin' => $request->pin,
                'url' => $request->url ?? 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc'
            ]);
            
            DB::commit();
            
            return [
                'message' => 'Software creado con éxito',
                'software' => $software,
            ];
        }
        catch (Exception $e) {
            DB::rollBack();
            
            return response([
                'message' => 'Internal Server Error',
                'payload' => $e->getMessage()
            ], 500);
        }
    }
    
    // Bearer xPlPAE1AmsM4xVoijA8C7nw2lfSPC1lIuO8w9OhD8JC4xFEHGknn3KTtvFtngquLrFcSVoFtw0oPiMrC
    /**
     * @OA\Put(
     *    tags={"Configuración"},
     *    path="/api/v2.1/config/certificate",
     *    summary="Datos del certificado",
     *    security={{"api_token":{}}},
     *    @OA\RequestBody(
     *        required=true,
     *        description="Objeto para la configuración del certificado.",
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                required={
     *                  "certificate",
     *                  "password"
     *                },
     *                @OA\Property(
     *                    property="certificate",
     *                    description="Certificado (.p12) en base64",
     *                    type="string",
     *                    format="byte"
     *                ),
     *                @OA\Property(
     *                    property="password",
     *                    description="Password del certificado",
     *                    type="string"
     *                ),
     *                example={
     *                    "certificate": "base64",
     *                    "password": "123456"
     *                }
     *            )
     *        )
     *    ),
     *    @OA\Response(
     *        response=200,
     *        description="OK",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=422,
     *        description="Unprocessable Entity",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Bad Request",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=401,
     *        description="Unauthorized",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=404,
     *        description="Not Found",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    ),
     *    @OA\Response(
     *        response=500,
     *        description="Internal Server Error",
     *        @OA\JsonContent(
     *            type="object",
     *            @OA\Items()
     *        ),
     *    )
     * )
     */
    public function storeCertificate(ConfigurationCertificateRequest $request) {
        try {
            if (!base64_decode($request->certificate, true)) throw new Exception('The given data was invalid.');
            if (!openssl_pkcs12_read($certificateBinary = base64_decode($request->certificate), $certificate, $request->password)) throw new Exception('The certificate could not be read.');
        }
        catch(Exception $e) {
            if (($error = openssl_error_string()) == false) {
                return response([
                    'message' => $e->getMessage(),
                    'errors' => [
                        'certificate' => 'The base64 encoding is not valid.',
                    ]
                ], 422);
            }
            
            return response([
                'message' => $e->getMessage(),
                'errors' => [
                    'certificate' => $error,
                    'password' => $error
                ]
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            auth()->user()->company->certificate()->delete();
            
            $company = auth()->user()->company;
            $name = "{$company->identification_number}{$company->dv}.p12";
            
            Storage::put("certificates/{$name}", $certificateBinary);
            
            $certificate = auth()->user()->company->certificate()->create([
                'name' => $name,
                'password' => $request->password
            ]);
            
            DB::commit();
            
            return [
                'message' => 'Certificado creado con éxito',
                'certificado' => $certificate,
            ];
        }
        catch (Exception $e) {
            DB::rollBack();
            
            return response([
                'message' => 'Internal Server Error',
                'payload' => $e->getMessage()
            ], 500);
        }
    }
}